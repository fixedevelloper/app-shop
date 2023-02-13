<?php


namespace App\Controller;


use App\Entity\Customer;
use App\Entity\User;

use App\Repository\CustomerRepository;
use App\Repository\SellerShopRepository;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Constraints\Timezone;

class AuthApiController extends AbstractFOSRestController
{
    private $passwordEncoder;
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $userRepository;
    private SellerShopRepository $selleRepository;
    private ShopRepository $shopRepository;
    private $doctrine;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param SellerShopRepository $sellerShopRepository
     * @param LoggerInterface $logger
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager,ShopRepository $shopRepository,
                                UserRepository $userRepository,SellerShopRepository $sellerShopRepository,
                              LoggerInterface $logger,UserPasswordHasherInterface $passwordEncoder)
    {
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository=$userRepository;
        $this->selleRepository=$sellerShopRepository;
        $this->shopRepository=$shopRepository;
        $this->doctrine=$entityManager;
    }

    /**
     * @Rest\Post("/v1/api_login", name="api_auth")
     * @param Request $request
     * @return Response
     */
    public function auth(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email=$data['username'];
        $password=$data['password'];
        $user=$this->userRepository->findOneBy(['email'=>$email]);
        if (null == $user) {
            $view = $this->view([], Response::HTTP_FORBIDDEN, []);
            return $this->handleView($view);
        }
        $isValid = $this->passwordEncoder->isPasswordValid($user, $password);
        if (!$isValid) {
            $view = $this->view([], Response::HTTP_FORBIDDEN, []);
            return $this->handleView($view);
        }
        $shop=$this->shopRepository->find($data['shop']);
        $seller=$this->selleRepository->findOneBy(['seller'=>$user,'shop'=>$shop]);
        $seller_id=null;
        if (!is_null($seller)){
            $seller_id=$seller->getId();
        }
        $body=[
            'id'=>$user->getId(),
            'name'=>$user->getName(),
            'username'=>$user->getEmail(),
            'password'=>$user->getPhone(),
            'email'=>$user->getEmail(),
            'phone'=>$user->getPhone(),
            'avatar'=>$user->getAvatar(),
            'roles'=>$user->getRoles(),
            'shop'=>$shop->getId(),
            'seller'=>$seller_id
        ];
        $view = $this->view($body, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/v1/api_login_customer", name="api_auth_customer")
     * @param Request $request
     * @return Response
     */
    public function authcustomer(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data=$res['data'];
        $email=$data['phone'];
        $password=$data['password'];
        $user=$this->userRepository->findOneBy(['phone'=>$email]);
        if (null == $user) {
            $view = $this->view([], Response::HTTP_FORBIDDEN, []);
            return $this->handleView($view);
        }
        $isValid = $this->passwordEncoder->isPasswordValid($user, $password);
        if (!$isValid) {
            $view = $this->view([], Response::HTTP_FORBIDDEN, []);
            return $this->handleView($view);
        }
        $customer=$this->customerRepository->findOneBy(['compte'=>$user]);
        if (is_null($customer)){
            $view = $this->view([], Response::HTTP_FORBIDDEN, []);
            return $this->handleView($view);
        }
        $body=[
            'id'=>$customer->getId(),
            'name'=>$user->getName(),
            'username'=>$user->getEmail(),
            'password'=>$user->getPhone(),
            'email'=>$user->getEmail(),
            'phone'=>$user->getPhone(),
            'avatar'=>"",
        ];
        $view = $this->view($body, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/users", name="api_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->logger->info($request->getContent());

        $res = json_decode($request->getContent(), true);
        $data=$res['data'];
        if (is_null($data['id'])){
            $user=new User();
            $user->setUsername($data['email']);
            $plainPassword = "12345";
            $hashedPassword = $this->passwordEncoder->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }else{
            $user=$this->userRepository->find($data['id']);
        }
        $user->setEmail($data['email']);
        $user->setName($data['name']);
            $user->setPhone($data['phone']);
        if ($data['role']=="seller"){
            $user->setRoles(["ROLE_SELLER"]);
        }elseif ($data['role']=="controller"){
            $user->setRoles(["ROLE_CONTROLLER"]);
        }else{
            $user->setRoles(["ROLE_ADMIN"]);
        }

        $user->setIsactivate(true);
        $this->doctrine->persist($user);

        $this->doctrine->flush();
        $body=[
            'id'=>$user->getId(),
            'name'=>$user->getName(),
            'username'=>$user->getEmail(),
            'password'=>$user->getPhone(),
            'email'=>$user->getEmail(),
            'phone'=>$user->getPhone(),
            'avatar'=>"",
        ];
        $view = $this->view($body, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/v1/changepassword",name="changepassword")
     * @param Request $request
     * @return Response
     */
    public function ChangePassword(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $user=$this->userRepository->find($body['id']);
        $oldpass = $body['password'];
        $newpassword = $body['cpassword'];
        $isValid = $this->passwordEncoder->isPasswordValid($user, $oldpass);
        if (!$isValid){
            throw new BadCredentialsException("Access not Authorized");
        }
        $hashedPassword = $this->passwordEncoder->hashPassword($user, $newpassword);
        $user->setPassword($hashedPassword);
        $this->doctrine->flush();
        $view = $this->view([
            'isvalid'=>$isValid,
            'user'=>$user->getId()
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/v1/forgetpassword",name="forgetpassword")
     * @param Request $request
     * @return Response
     */
    public function forgetPassword(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email=$data['email'];
        $user=$this->userRepository->findOneBy(['email'=>$email]);
        if (null == $user) {
            throw new BadCredentialsException("Resource $email not found");
        }
        $view = $this->view([
            'message'=>"Mail send to your email",
            'code'=>200
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/profile/{id}",name="api_profile")
     * @param User $user
     * @return Response
     */
    public function api_profile(User $user)
    {
        $customer=$this->customerRepository->findOneBy(['compte'=>$user]);
        $view = $this->view([
            'email'=>$user->getEmail(),
            'name'=>$user->getName(),
            'phone'=>$user->getPhone(),
            'avatar'=>$user->getAvatar(),
            'customer'=>$customer->getId(),
            'validitydate'=>$customer->getExpiredAt()->format("Y-m-d"),
            'totalsouscription'=>count($this->souscriptionRepository->findByCustomer($customer)),
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
}
