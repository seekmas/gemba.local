<?php

namespace Makoto\FrontBundle\Controller;

use Makoto\FrontBundle\Entity\Message;
use Makoto\FrontBundle\Entity\Subscriber;
use Makoto\FrontBundle\Form\MessageType;
use Makoto\FrontBundle\Form\SubscriberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $subscriber = new Subscriber();
        $subscriberType = new SubscriberType();
        $subscriberForm = $this->createForm($subscriberType,$subscriber);
        $subscriberForm->handleRequest($request);
        if($subscriberForm->isValid())
        {
            $data = $subscriberForm->getData();

            $exist = $this->getDoctrine()->getRepository('MakotoFrontBundle:Subscriber')->findOneBy(['email' => $data->getEmail()]);
            if($exist)
            {
                $request->getSession()->getFlashBag()->add('success' , '这个Email已经订阅过了');
                return $this->redirect($this->generateUrl('makoto_front_homepage'));
            }

            $subscriber->setCreatedAt( new \Datetime());
            $em->persist($subscriber);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success' , '订阅成功');
            return $this->redirect($this->generateUrl('makoto_front_homepage'));
        }

        $message = new Message();
        $messageType = new MessageType();

        $messageForm = $this->createForm($messageType,$message);
        $messageForm->handleRequest($request);
        if($messageForm->isValid())
        {
            $message->setCreatedAt(new \Datetime());
            $em->persist($message);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success' , '咨询消息发送成功 我们稍后就会处理 谢谢你的支持');
            return $this->redirect($this->generateUrl('makoto_front_homepage'));
        }

        return $this->render('MakotoFrontBundle:Default:index.html.twig',
            [
                'subscriberForm'=>$subscriberForm->createView() ,
                'messageForm' => $messageForm->createView() ,
            ]
        );
    }

    public function mapAction()
    {
        return $this->render('MakotoFrontBundle:Default:map.html.twig');
    }
}
