<?php
/**
 * This file is part of the Claroline Connect package
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * Author: Panagiotis TSAVDARIS
 * 
 * Date: 4/24/15
 */

namespace Icap\SocialmediaBundle\Listener;

use Claroline\CoreBundle\Event\CustomActionResourceEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class ResourceActionsListener
 * @package Icap\SocialmediaBundle\Listener
 *
 * @DI\Service
 */
class ResourceActionsListener
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    private $securityContext;

    /**
     * @DI\InjectParams({
     *     "requestStack"       = @DI\Inject("request_stack"),
     *     "httpKernel"         = @DI\Inject("http_kernel"),
     *     "securityContext"    = @DI\Inject("security.context")
     * })
     */
    public function __construct(
        RequestStack $requestStack,
        HttpKernelInterface $httpKernel,
        SecurityContextInterface $securityContext
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->httpKernel = $httpKernel;
        $this->securityContext = $securityContext;
    }

    /**
     * @DI\Observe("resource_action_like_action")
     */
    public function onLikeAction(CustomActionResourceEvent $event)
    {
        $this->redirect(
            [
                '_controller'   => 'IcapSocialmediaBundle:LikeAction:form',
                'resourceId'    => $event->getResource()->getResourceNode()->getId(),
                'user'          => $this->securityContext->getToken()->getUser()
            ],
            $event
        );
    }

    /**
     * @DI\Observe("resource_action_share_action")
     */
    public function onShareAction(CustomActionResourceEvent $event)
    {
        $this->redirect(
            [
                '_controller'   => 'IcapSocialmediaBundle:ShareAction:form',
                'resourceId'    => $event->getResource()->getResourceNode()->getId(),
                'user'          => $this->securityContext->getToken()->getUser()
            ],
            $event
        );
    }

    /**
     * @DI\Observe("resource_action_comment_action")
     */
    public function onCommentAction(CustomActionResourceEvent $event)
    {
        $this->redirect(
            [
                '_controller'   => 'IcapSocialmediaBundle:CommentAction:form',
                'resourceId'    => $event->getResource()->getResourceNode()->getId(),
                'user'          => $this->securityContext->getToken()->getUser()
            ],
            $event
        );
    }

    /**
     * @DI\Observe("resource_action_note_action")
     */
    public function onNoteAction(CustomActionResourceEvent $event)
    {
        $this->redirect(
            [
                '_controller'   => 'IcapSocialmediaBundle:NoteAction:noteForm',
                'resourceId'    => $event->getResource()->getResourceNode()->getId(),
                'user'          => $this->securityContext->getToken()->getUser()
            ],
            $event
        );
    }

    protected function redirect($params, $event)
    {
        $subRequest = $this->request->duplicate(array(), null, $params);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        $event->setResponse($response);
        $event->stopPropagation();
    }
} 