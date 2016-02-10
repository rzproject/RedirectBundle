<?php

namespace Rz\RedirectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class RedirectAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('fromPath')
            ->add('toPath')
            ->add('enabled')
            ->add('updatedAt')
            ->add('createdAt')
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('fromPath', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('toPath', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('enabled', null, array('editable' => true, 'footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('updatedAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('createdAt', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('fromPath')
            ->add('toPath')
            ->add('enabled')
            ->add('updatedAt', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
            ->add('createdAt', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Redirect', array('class' => 'col-md-8'))
                ->add('fromPath', 'text')
                ->add('toPath', 'text')
            ->end()
            ->with('Options', array('class' => 'col-md-4'))
                ->add('name', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
            ->end();
        ;
    }
    /**
     * @{inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        // find object with toLink
        $fromPath = $this->modelManager->findOneBy($this->getClass(), array('fromPath' => $object->getFromPath()));
        // @formatter:off
        if (null !== $fromPath && $fromPath->getId() !== $object->getId()) {
            $errorElement
                ->with('fromPath')
                    ->addViolation('This link is already being redirected somewhere else!')
                ->end();
        }
        if(substr($object->getToPath(), 0, 1) !== '/'){
            $errorElement
                ->with('toPath')
                    ->addViolation('Invalid path! A path start with a "/"')
                ->end();
        }
        if(substr($object->getFromPath(), 0, 1) !== '/'){
            $errorElement
                ->with('fromPath')
                    ->addViolation('Invalid path! A path start with a "/"')
                ->end();
        }
        // @formatter:on
    }
}