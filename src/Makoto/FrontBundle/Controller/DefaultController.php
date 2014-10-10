<?php

namespace Makoto\FrontBundle\Controller;

use Makoto\FrontBundle\Entity\Message;
use Makoto\FrontBundle\Entity\Subscriber;
use Makoto\FrontBundle\Form\MessageType;
use Makoto\FrontBundle\Form\SubscriberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function voteAction()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=gb2312">';
        //http://xjeylyef.com/vote/Vote_save.asp?zjhm=370281199308175725



        $ch = curl_init();
        $ip = $this->rand_ip();
        $header = array(
            'CLIENT-IP:'.$ip,
            'X-FORWARDED-FOR:'.$ip,
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt ($ch, CURLOPT_URL, "http://xjeylyef.com/vote/Vote_save.asp?zjhm=370281199308175725");
        curl_setopt ($ch, CURLOPT_URL, "http://xjeylyef.com/vote/Vote_save.asp?zjhm=370102199512020316");

        curl_setopt ($ch, CURLOPT_REFERER, "http://xjeylyef.com");
        curl_exec ($ch);
        curl_close ($ch);





        return new Response(date('Y-m-d H:i:s'));
    }

    function rand_ip(){
        $ip_long = array(
            array('607649792', '608174079'),
            array('1038614528', '1039007743'),
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $rand_key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $ip;
    }
}
