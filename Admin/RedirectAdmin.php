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
    protected $types;

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
            ->add('redirect', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('xs', 'sm')))))
            ->add('publicationDateStart', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
            ->add('publicationDateEnd', null, array('footable'=>array('attr'=>array('data-breakpoints'=>array('all')))))
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
            ->add('redirect', 'doctrine_orm_choice', array(), 'choice', array('choices'=>$this->getTypes()))
            ->add('publicationDateStart', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
            ->add('publicationDateEnd', 'doctrine_orm_datetime_range', array('field_type' => 'sonata_type_datetime_range_picker'))
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
                ->add('name', null, array('required' => false))
            ->end()
            ->with('Options', array('class' => 'col-md-4'))
                ->add('redirect',  'choice', array('choices'=>$this->getTypes(), 'required' => true))
                ->add('enabled', null, array('required' => false))
                ->add('publicationDateStart', 'sonata_type_datetime_picker', array('dp_side_by_side' => true))
                ->add('publicationDateEnd', 'sonata_type_datetime_picker', array('required' => false, 'dp_side_by_side' => true))
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
        if (substr($object->getToPath(), 0, 1) !== '/') {
            $errorElement
                ->with('toPath')
                    ->addViolation('Invalid path! A path start with a "/"')
                ->end();
        }
        if (substr($object->getFromPath(), 0, 1) !== '/') {
            $errorElement
                ->with('fromPath')
                    ->addViolation('Invalid path! A path start with a "/"')
                ->end();
        }
        // @formatter:on
    }

    /**
     * @return mixed
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param mixed $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }
}
