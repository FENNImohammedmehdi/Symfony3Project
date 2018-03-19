<?php

namespace MonMarsupilami\MonMarsupilamiBundle\Controller\Marsupilamis;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder; 
class MarsupilamiController extends Controller{
	
	//Rest api
    

    
    public function showMarsupilamisAction($name) {

        $em = $this->getDoctrine()->getManager();
        $marsupilami = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(
        ['username' =>$name]);
        if (empty($marsupilami)) {
            $response = array(
                'code' => 1,
                'message' => 'Marsupilami not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data = $this->get('jms_serializer')->serialize($marsupilami, 'json');
        $response = array(
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => json_decode($data)
        );
        return new JsonResponse($response, 200);
    }
   
    public function listMarsupilamisAction(){
        $em = $this->getDoctrine()->getManager();
        $marsupilami = $em->getRepository('UtilisateursBundle:Utilisateurs')->findAll();
        if (!count($marsupilami)){
            $response=array(
                'code'=>1,
                'message'=>'No Marsupilamis found!',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data=$this->get('jms_serializer')->serialize($marsupilami,'json');
        $response=array(
            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)
        );
        return new JsonResponse($response,200);
    }

    public function listAmisMarsupilamisAction($id){
        $em = $this->getDoctrine()->getManager();
        $marsupilami = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
        $marsupilami = $marsupilami->getFriendsWithMe();
        if (!count($marsupilami)){
            $response=array(
                'code'=>1,
                'message'=>'No Marsupilamis found!',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data=$this->get('jms_serializer')->serialize($marsupilami,'json');
        $response=array(
            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)
        );
        return new JsonResponse($response,200);
    }
    
    public function deleteMarsupilamisAction($id){

        $marsupilami=$this->getDoctrine()->getRepository('UtilisateursBundle:Utilisateurs')->find($id);

        if (empty($marsupilami)) {
            $response=array(
                'code'=>1,
                'message'=>'Marsupilami Not found !',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($marsupilami);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Marsupilami deleted !',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,200);
    }
   
    public function createMarsupilamisAction(Request $request) {
        
        $encoder = new BCryptPasswordEncoder(4);
        $data = $request->getContent();
        $marsupilami = $this->get('jms_serializer')->deserialize($data, 'Utilisateurs\UtilisateursBundle\Entity\Utilisateurs', 'json');
        if (empty($marsupilami)) {
            $response = array(
                'code' => 1,
                'message' => 'Marsupilami not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $plainPassword = $marsupilami->getPassword();
        $encoded = $encoder->encodePassword($plainPassword,null);
        $marsupilami->setPassword($encoded);
        $marsupilami->setEnabled(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($marsupilami);
        $em->flush();
        $response = array(
            'code' => 0,
            'message' => 'Marsupilami created!',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, Response::HTTP_CREATED);
        
    }    

    public function updateMarsupilamisAction(Request $request,$id){
           
        $data = $request->getContent();
        $marsupilami = $this->get('jms_serializer')->deserialize($data, 
                'Utilisateurs\UtilisateursBundle\Entity\Utilisateurs', 'json');
        if (empty($marsupilami)) {
            $response = array(
                'code' => 1,
                'message' => 'Marsupilami not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
           $race = $marsupilami->getRace();
           $famille  = $marsupilami->getFamille();
           $age = $marsupilami->getAge();
           $nourriture  = $marsupilami->getNourriture();
           $em = $this->getDoctrine()->getManager();
           $query = $em->createQuery('UPDATE UtilisateursBundle:Utilisateurs m '
                    . 'SET m.age = :nvAge ,'
                    . ' m.famille = :nvFamille ,'
                    . ' m.race = :nvRace ,'
                    . ' m.nourriture = :nvNourriture '
                    . 'WHERE m.id = :idModele');
            $query->setParameter('nvAge', $age);
            $query->setParameter('nvFamille',$famille);
            $query->setParameter('nvRace', $race);
            $query->setParameter('nvNourriture',$nourriture);
            $query->setParameter('idModele', $id);
            $query->execute();
                   $response = array(
            'code' => 0,
            'message' => 'Marsupilami updated!',
            'errors' => null,
            'result' => null
                );
        return new JsonResponse($response, Response::HTTP_CREATED);
         

                    
    }
   
    public function inviteFriendAction(Request $request,$name) {
       
        $data = $request->getContent();
        $friend = $this->get('jms_serializer')->deserialize($data, 'Utilisateurs\UtilisateursBundle\Entity\Utilisateurs', 'json');
        if (empty($friend)) {
            $response = array(
                'code' => 1,
                'message' => 'Marsupilami not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $friend1 = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($friend->getId());
        $user = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(
        ['username' =>$name]);
       
        $friend1->addMyFriends($user);
        $em->persist($user);
                
        $user->setAllFriends();
        $em->persist($user);
        $em->flush();
        
        
         $response = array(
            'code' => 0,
            'message' => 'Marsupilami invited!'.$user.'  '.$friend,
            'errors' => null,
            'result' => null
                );
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    public function deleteFriendAction(Request $request,$name) {
       
        $data = $request->getContent();
        $friend = $this->get('jms_serializer')->deserialize($data, 'Utilisateurs\UtilisateursBundle\Entity\Utilisateurs', 'json');
         if (empty($friend)) {
            $response = array(
                'code' => 1,
                'message' => 'Marsupilami not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $friend1 = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($friend->getId());
        $user = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(
        ['username' =>$name]);
            $user->removeMyFriend($friend1);
            $friend1->removeMyFriend($user);
            $em->persist($user);
            $em->persist($friend1);
            $em->flush();
        
         $response = array(
            'code' => 0,
            'message' => 'marsupilami friendship deleted!'.$user.'  '.$friend,
            'errors' => null,
            'result' => null
                );
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    
	
	
    
    public function listesAction() {

        $em = $this->getDoctrine()->getManager();

        $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->findAll();

        $marsupilamis2 = $this->getUser()->getFriendsWithMe();

        return $this->render('MonMarsupilamiBundle:Default\Marsupilami:listes.html.twig', array('marsupilamis' => $marsupilamis, 'marsupilamis2' => $marsupilamis2)
        );
    }

    public function detailsAction($id) {

        $em = $this->getDoctrine()->getManager();
        $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
        return $this->render('MonMarsupilamiBundle:Default\Marsupilami:details.html.twig', array('marsupilamis' => $marsupilamis));
    }

    public function invitationAction($id) {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {

            $friend = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            if (!$friend) {
                throw $this->createNotFoundException(
                        'No user found for id ' . $id
                );
            }

            $user = $this->getUser();
            $friend->addMyFriends($user);
            $user->setAllFriends();

            $em->persist($user);
            $em->flush();

            $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->findAll();
            $marsupilamis2 = $this->getUser()->getFriendsWithMe();
            return $this->redirectToRoute('mon_marsupilami_listes', array('marsupilamis' => $marsupilamis, 'marsupilamis2' => $marsupilamis2));
        } else {
            $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            return $this->render('MonMarsupilamiBundle:Default\Marsupilami:invitation.html.twig', array('marsupilamis' => $marsupilamis));
        }
    }

    public function anullation_amitieAction($id) {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {

            $friend = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            if (!$friend) {
                throw $this->createNotFoundException(
                        'No user found for id ' . $id
                );
            }

            $user = $this->getUser();
            $user->removeMyFriend($friend);
            $friend->removeMyFriend($user);
            $em->persist($user);
            $em->persist($friend);
            $em->flush();

            $marsupilamis = null;
            $marsupilamis2 = null;
            return $this->redirectToRoute('mon_marsupilami_listes', array('marsupilamis' => $marsupilamis, 'marsupilamis2' => $marsupilamis2));
        } else {
            $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            return $this->render('MonMarsupilamiBundle:Default\Marsupilami:anullation_amitie.html.twig', array('marsupilamis' => $marsupilamis));
        }
    }

    public function modificationAction($id) {

        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $entity = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            $query = $em->createQuery('UPDATE UtilisateursBundle:Utilisateurs m '
                    . 'SET m.age = :nvAge ,'
                    . ' m.famille = :nvFamille ,'
                    . ' m.race = :nvRace ,'
                    . ' m.nourriture = :nvNourriture '
                    . 'WHERE m.id = :idModele');
            $query->setParameter('nvAge', $request->request->get('age'));
            $query->setParameter('nvFamille', $request->request->get('famille'));
            $query->setParameter('nvRace', $request->request->get('race'));
            $query->setParameter('nvNourriture', $request->request->get('nourriture'));
            $query->setParameter('idModele', $id);
            $query->execute();
            $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->findAll();
            $marsupilamis2 = $this->getUser()->getFriendsWithMe();
            return $this->redirectToRoute('mon_marsupilami_listes', array('marsupilamis' => $marsupilamis, 'marsupilamis2' => $marsupilamis2));



        } else {
            $entity = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            return $this->render('MonMarsupilamiBundle:Default\Marsupilami:modification.html.twig', array('entity' => $entity));
        }
    }

    public function suppressionAction($id) {

        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            $em->remove($entity);
            $em->flush();

            $marsupilamis = null;
            $marsupilamis2 = null;
            return $this->redirectToRoute('mon_marsupilami_listes', array('marsupilamis' => $marsupilamis, 'marsupilamis2' => $marsupilamis2));
        } else {
            $marsupilamis = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($id);
            return $this->render('MonMarsupilamiBundle:Default\Marsupilami:suppression.html.twig', array('marsupilamis' => $marsupilamis)
            );
        }
    }

}
