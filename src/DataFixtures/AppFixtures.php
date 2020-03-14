<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Helper;
use App\Entity\HelpRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
        $this->loadHelpers($manager);
        $this->loadHelpRequests($manager);

        $manager->flush();
    }

    private function loadAdmin(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->username = 'tgalopin';
        $admin->setPassword($this->encoder->encodePassword($admin, 'password'));

        $manager->persist($admin);
    }

    private function loadHelpers(ObjectManager $manager)
    {
        $helpers = [
            [
                'firstName' => 'Elizabeth',
                'lastName' => 'Gregory',
                'email' => 'elizabeth.gregory@example.com',
                'zipCode' => '75008',
                'haveChildren' => false,
                'canBabysit' => true,
                'babysitMaxChildren' => 1,
                'babysitAgeRanges' => null,
                'canBuyGroceries' => true,
            ],
        ];

        foreach ($helpers as $data) {
            $helper = new Helper();
            $helper->firstName = $data['firstName'];
            $helper->lastName = $data['lastName'];
            $helper->email = $data['email'];
            $helper->zipCode = $data['zipCode'];
            $helper->haveChildren = $data['haveChildren'];
            $helper->canBabysit = $data['canBabysit'];
            $helper->babysitMaxChildren = $data['babysitMaxChildren'];
            $helper->babysitAgeRanges = $data['babysitAgeRanges'];
            $helper->canBuyGroceries = $data['canBuyGroceries'];

            $manager->persist($helper);
        }
    }

    private function loadHelpRequests(ObjectManager $manager)
    {
        $requests = [
            [
                'helpType' => HelpRequest::TYPE_BABYSIT,
                'ownerUuid' => 'cd34e489-ec5a-4fb7-8fa5-e36554f1cd6c',
                'firstName' => 'Jeanne',
                'lastName' => 'Martin',
                'email' => 'jeanne.martin@example.com',
                'zipCode' => '75008',
                'jobType' => 'health',
                'childAgeRange' => HelpRequest::AGE_RANGE_35,
            ],
            [
                'helpType' => HelpRequest::TYPE_GROCERIES,
                'ownerUuid' => 'cd34e489-ec5a-4fb7-8fa5-e36554f1cd6c',
                'firstName' => 'Jeanne',
                'lastName' => 'Martin',
                'email' => 'jeanne.martin@example.com',
                'zipCode' => '75008',
                'jobType' => 'health',
                'childAgeRange' => null,
            ],
            [
                'helpType' => HelpRequest::TYPE_GROCERIES,
                'ownerUuid' => '4c4813df-ac99-4484-9cde-fdda1a7a910d',
                'firstName' => 'Catherine',
                'lastName' => 'Lambert',
                'email' => 'catherine.lambert@example.com',
                'zipCode' => '75010',
                'jobType' => 'food',
                'childAgeRange' => null,
            ],
        ];

        foreach ($requests as $data) {
            $request = new HelpRequest();
            $request->helpType = $data['helpType'];
            $request->ownerUuid = Uuid::fromString($data['ownerUuid']);
            $request->firstName = $data['firstName'];
            $request->lastName = $data['lastName'];
            $request->email = $data['email'];
            $request->zipCode = $data['zipCode'];
            $request->jobType = $data['jobType'];
            $request->childAgeRange = $data['childAgeRange'];

            $manager->persist($request);
        }
    }
}
