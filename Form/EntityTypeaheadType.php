<?php

/*
 * This file is part of the BluemesaCoreBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Form;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

/**
 * Bootstrap entity typeahead form control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class EntityTypeaheadType extends AbstractType
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Construct EntityTypeaheadType
     * 
     * @DI\InjectParams({
     *     "router" = @DI\Inject("router")
     * })
     * 
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data_link = $options['data_link'];
        $data_route = $options['data_route'];
        $data_route_options = $options['data_route_options'];

        if (null === $data_link) {
            if (null === $data_route) {
                $data_link = $this->router->generate('BluemesaFormsBundle_ajax_choices',
                    array('class' => $options['class'],
                          'property' => $options['choice_label']));
            } else {
                $data_link = $this->router->generate($data_route, $data_route_options);
            }
        }

        $view->vars['data_link'] = $data_link;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_link' => null,
            'data_route' => null,
            'data_route_options' => array()
        ));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return TextEntityType::class;
    }
}
