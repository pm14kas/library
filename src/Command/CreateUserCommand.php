<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\User;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("user:create")->setDescription("Creates a user")
            ->addArgument("login", InputArgument::REQUIRED, "Username")
            ->addArgument("password", InputArgument::REQUIRED, "User password")
            ->addArgument("email", InputArgument::REQUIRED, "Email of user, must have format \"user@server.domain\"");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument("login");
        $password = $input->getArgument("password");
        $email = $input->getArgument("email");
        $manager = $this->getContainer()->get("doctrine")->getManager();
        
        try {
            $user = new User();
            $user->setUserName($name);
            $user->setPlainPassword($password);
            $user->setEmail($email);
            $user->setEnabled(true);

            $manager->persist($user);
            $manager->flush();
            $output->writeln("User ".$user->getUserName()." created successfully");
        } catch (\Exception $e) {
            $output->writeln("Failed to create user:\n".$e->getMessage());
        }
    }
}
