<?php

namespace Rz\RedirectBundle\Model;

abstract class Redirect implements RedirectInterface
{
    protected $name;

    protected $enabled;

    protected $fromPath;

    protected $toPath;

    protected $createdAt;

    protected $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $name = $this->name;

        if($name === null){
            $name = '(unnamed redirect)';
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setFromPath($path)
    {
        $this->fromPath = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromPath()
    {
        return $this->fromPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setToPath($path)
    {
        $this->toPath = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getToPath()
    {
        return $this->toPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}