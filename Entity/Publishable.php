<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity;

use Symfony\Component\Validator\ExecutionContext;

/**
 * Publishable element
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class Publishable
{

    /**
     * @var \DateTime
     */
    protected $startedAt;

    /**
     * @var \DateTime
     */
    protected $endedAt;

    /**
     * Get publication state
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function getPublication()
    {
        if (is_null($this->getStartedAt()) && is_null($this->getEndedAt())) {
            return 'Never published';
        } elseif (!is_null($this->getStartedAt()) && $this->getStartedAt()->getTimestamp() <= time() && is_null($this->getEndedAt())) {
            return 'Published';
        } elseif (!is_null($this->getStartedAt()) && $this->getStartedAt()->getTimestamp() > time()) {
            return 'Pre-published';
        } elseif (!is_null($this->getStartedAt()) && $this->getStartedAt()->getTimestamp() < time() && !is_null($this->getEndedAt()) && $this->getEndedAt()->getTimestamp() < time()) {
            return 'Post-published';
        } elseif (!is_null($this->getStartedAt()) && $this->getStartedAt()->getTimestamp() <= time() && !is_null($this->getEndedAt()) && $this->getEndedAt()->getTimestamp() >= time()) {
            return 'Published temp';
        }
    }

    /**
     * Check if publication is correct
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param ExecutionContext $context
     */
    public function isPublicationValid(ExecutionContext $context)
    {
        // No start publication date specified for the end publication date
        if (!$this->getStartedAt() && $this->getEndedAt()) {
            $context->addViolationAt('startedAt', 'Start publication date is required when setting end publication date.');
        }
        // Start publication date is equal or later than end publication date
        if ($this->getEndedAt() && $this->getStartedAt() && $this->getStartedAt()->getTimestamp() >= $this->getEndedAt()->getTimestamp()) {
            $context->addViolationAt('endedAt', 'End publication date must be later than start publication date.');
        }
    }

    /**
     * Check if element is published
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isPublished()
    {
        return $this->getStartedAt()
            && $this->getStartedAt()->getTimestamp() <= time()
            && (!$this->getEndedAt() || $this->getEndedAt()->getTimestamp() >= time());
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return Block
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set endedAt
     *
     * @param \DateTime $endedAt
     *
     * @return Block
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }
}
