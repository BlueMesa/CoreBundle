<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Doctrine;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManagerDecorator;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Bluemesa\Bundle\CoreBundle\Repository\EntityRepository;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface;
use VIB\SecurityBundle\Bridge\Doctrine\AclFilter;

/**
 * ACL aware implementation of Doctrine\Common\Persistence\ObjectManagerDecorator
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.core.doctrine.manager")
 * @DI\Tag("bluemesa_core.object_manager")
 */
class ObjectManager extends ObjectManagerDecorator
{

    /**
     * @var Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var Symfony\Component\Security\Acl\Model\MutableAclProviderInterface
     */
    protected $aclProvider;
    
    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;
    
    /**
     * @var boolean
     */
    protected $isAutoAclEnabled;
    
    /**
     * Construct ObjectManager
     *
     * @DI\InjectParams({
     *     "managerRegistry" = @DI\Inject("doctrine"),
     *     "userProvider" = @DI\Inject("user_provider"),
     *     "aclProvider" = @DI\Inject("security.acl.provider"),
     *     "securityContext" = @DI\Inject("security.context", required=false)
     * })
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry                $managerRegistry
     * @param Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param Symfony\Component\Security\Acl\Model\AclProviderInterface  $aclProvider
     * @param Symfony\Component\Security\Core\SecurityContextInterface   $securityContext
     */
    public function __construct(ManagerRegistry $managerRegistry,
                                UserProviderInterface $userProvider,
                                MutableAclProviderInterface $aclProvider,
                                SecurityContextInterface $securityContext = null)
    {
        $this->wrapped = $managerRegistry->getManager();
        $this->userProvider = $userProvider;
        $this->aclProvider = $aclProvider;
        $this->securityContext = $securityContext;
        $this->isAutoAclEnabled = true;
    }
    
    /**
     * Get the class this Manager is used for
     * 
     * @return string
     */
    public function getManagedClass()
    {
        return 'Bluemesa\Bundle\CoreBundle\Entity\Entity';
    }
    
    /**
     * Create ACL for object(s)
     *
     * @param object $objects
     * @param array  $acl_param
     */
    public function createACL($objects, $acl_param = null)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->createACL($object, $acl_param);
            }
        } else {
            $object = $objects;
            if (null === $acl_param) {
                $acl_param = $this->getDefaultACL($object);
            } elseif (($user = $acl_param) instanceof UserInterface) {
                $acl_param = $this->getDefaultACL($object, $user);
            }
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            $aclProvider = $this->aclProvider;
            $acl = $aclProvider->createAcl($objectIdentity);
            $this->insertAclEntries($acl, $acl_param);
            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * Delete ACL for object(s)
     *
     * @param object $objects
     */
    public function removeACL($objects)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->removeACL($object);
            }
        } else {
            $object = $objects;
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            $aclProvider = $this->aclProvider;
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {}
        }
    }
    
    /**
     * Update ACL for object(s)
     *
     * @param object $objects
     */
    public function updateACL($objects, array $acl_array)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->updateACL($object, $acl_array);
            }
        } else {
            $object = $objects;
            $aclProvider = $this->aclProvider;
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
                $diff = $this->diffACL($acl, $acl_array);
                $this->updateAclEntries($acl, $diff['update']);
                $this->deleteAclEntries($acl, $diff['delete']);
                $this->insertAclEntries($acl, $diff['insert']);
                $aclProvider->updateAcl($acl);
            } catch (AclNotFoundException $e) {
                $this->createACL($object, $acl_array);
            }
        }
    }
    
    /**
     * Get ACL for object
     *
     * @return array
     */
    public function getACL($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $aclProvider = $this->aclProvider;
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $acl_array = array();
            foreach ($acl->getObjectAces() as $index => $ace) {
                $identity = $this->resolveIdentity($ace);
                $acl_array[$index] = array('identity' => $identity, 'permission' => $ace->getMask());
            }
        } catch (AclNotFoundException $e) {
            
            return array();
        }
        
        return $acl_array;
    }
    
    /**
     * Get object's owner
     *
     * @param  object                                             $object
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwner($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's owner
     *
     * @param  object                                             $objects
     * @param  Symfony\Component\Security\Core\User\UserInterface $owner
     */
    public function setOwner($objects, $owner)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setOwner($object, $owner);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $owner_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                    $owner_found = true;
                    if ($owner instanceof UserInterface) {
                        $acl_array[$index]['identity'] = $owner;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$owner_found) {
                $acl_array[]= array('identity' => $owner, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }
    
    /**
     * Get object's group
     *
     * @param  object $object
     * @return string
     */
    public function getGroup($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's group
     *
     * @param  object                                             $objects
     * @param  string                                             $group
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function setGroup($objects, $group)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setGroup($object, $group);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $group_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                    $group_found = true;
                    if (is_string($group)) {
                        $acl_array[$index]['identity'] = $group;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$group_found) {
                $acl_array[]= array('identity' => $group, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = $this->wrapped->getRepository($className);

        if (! $repository instanceof EntityRepository) {
            throw new \ErrorException('Repository must be an instance of Bluemesa\Bundle\CoreBundle\Repository\EntityRepository');
        }

        return $repository;
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param string $className The class name of the object to find.
     * @param mixed  $id        The identity of the object to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface $filter
     * 
     * @return object The found object.
     */
    public function find($className, $id, $filter = null)
    {
        if (($filter !== null)&&(! $filter instanceof EntityFilterInterface)) {
            throw new \InvalidArgumentException('Argument 3 passed to '
                    . get_class($this) . ' must be an instance of '
                    . 'Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface, '
                    . ((($type = gettype($filter)) == 'object') ? get_class($filter) : $type)
                    . ' given');
        }
        
        $repository = $this->getRepository($className);
        
        return $repository->getEntity($id, $filter);
    }

    /**
     * Finds all entities of the specified type.
     *
     * @param string $className The class name of the objects to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return Doctrine\Common\Collections\Collection The entities.
     */
    public function findAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getList($filter);
    }

    /**
     * Counts all entities of the specified type.
     * 
     * @param string $className The class name of the objects to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return integer Number of entities.
     */
    public function countAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getListCount($filter);
    }
    
    /**
     * Enable automatic ACL setting
     */
    public function enableAutoAcl()
    {
        $this->isAutoAclEnabled = true;
    }
    
    /**
     * Disable automatic ACL setting
     */
    public function disableAutoAcl()
    {
        $this->isAutoAclEnabled = false;
    }
    
    /**
     * Is automatic ACL setting enabled
     * 
     * @return boolean
     */
    public function isAutoAclEnabled()
    {
        return $this->isAutoAclEnabled;
    }
    
    /**
     * Resolve ACE itentity to User or Role
     * 
     * @param type $ace
     * @return mixed
     */
    protected function resolveIdentity($ace)
    {
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof UserSecurityIdentity) {
            $userProvider = $this->userProvider;
            try {
                
                return $userProvider->loadUserByUsername($securityIdentity->getUsername());
            } catch (UsernameNotFoundException $e) {
                
                return null;
            }
        } elseif ($securityIdentity instanceof RoleSecurityIdentity) {
            
            return $securityIdentity->getRole();
        }
    }
    
    /**
     * Compare ACLs
     * 
     * @param type $acl
     * @param array $acl_array
     * @return array
     */
    protected function diffACL($acl, array $acl_array)
    {
        $insert = $acl_array;
        $update = array();
        $delete = array();
        foreach ($acl->getObjectAces() as $index => $ace) {
            $identity = $this->resolveIdentity($ace);
            $mask = $ace->getMask();
            $found = false;
            foreach ($acl_array as $key => $acl_entry) {
                if ($acl_entry['identity'] == $identity) {
                    $found = true;
                    if ($acl_entry['permission'] != $mask) {
                        $update[$index] = $acl_entry;
                    }
                    unset($insert[$key]);
                }
            }
            if (! $found) {
                $delete[$index] = array('identity' => $identity, 'permission' => $mask);
            }
        }
        
        return array('insert' => $insert, 'update' => $update, 'delete' => $delete);
    }
    
    /**
     * Update ACL entries
     * 
     * @param type $acl
     * @param array $update
     */
    protected function updateAclEntries($acl, array $update)
    {
        foreach ($update as $index => $entry) {
            $acl->updateObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * Delete ACL entries
     * 
     * @param type $acl
     * @param array $delete
     */
    protected function deleteAclEntries($acl, array $delete)
    {
        foreach (array_reverse($delete, true) as $index => $entry) {
            $acl->deleteObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * Insert ACL entries
     * 
     * @param type $acl
     * @param array $insert
     */
    protected function insertAclEntries($acl, array $insert)
    {
        foreach ($insert as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if ($identity instanceof UserInterface) {
                $identity = UserSecurityIdentity::fromAccount($identity);
            } elseif (is_string($identity)) {
                $identity = new RoleSecurityIdentity($identity);
            }
            $acl->insertObjectAce($identity, $permission);
        }
    }
    
    /**
     * Get user from the Security Context
     *
     * @throws \LogicException If SecurityBundle is not available
     * 
     * @return mixed
     */
    protected function getUser()
    {
        if (null === $this->securityContext) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->securityContext->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
    
    /**
     * Get default ACL
     * 
     * @param object                                               $object
     * @param Symfony\Component\Security\Core\User\UserInterface $user
     * @return array
     */
    public function getDefaultACL($object = null, $user = null)
    {
        $user = (null === $user) ? $this->getUser() : $user;
        $acl = array();
        
        if (null !== $user) {
            $acl[] = array('identity' => $user,
                           'permission' => MaskBuilder::MASK_OWNER);
        }
        
        $acl[] = array('identity' => 'ROLE_USER',
                       'permission' => MaskBuilder::MASK_VIEW);
        
        return $acl;
    }
}
