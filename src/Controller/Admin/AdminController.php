<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
    protected UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function createNewAdminEntity(): admin
    {
        return (new Admin())
            ->setId(1)
            ->setUsername('');
    }

    protected function persistAdminEntity(Admin $admin): void
    {
        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, $admin->getPlainPassword())
        );
        parent::persistEntity($admin);
    }

    protected function updateAdminEntity(Admin $admin): void
    {
        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, $admin->getPlainPassword())
        );
        parent::updateEntity($admin);
    }

    protected function removeAdminEntity(Admin $admin)
    {
        if ($admin->getUsername() === $this->getUser()->getUsername()) {
            $this->addFlash('error', 'You cannot delete yourself.');

            return $this->redirectToRoute('easyadmin', ['action' => 'list', 'entity' => $this->entity['name']]);
        }

        parent::removeEntity($admin);
    }
}
