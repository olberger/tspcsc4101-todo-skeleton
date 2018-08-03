<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


/**
 * Command Todo
 */
class ListTodosCommand extends ContainerAwareCommand
{    
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:list-todos')
        
        // the short description shown while running "php bin/console list"
        ->setDescription('List the todos.')
        
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to list the todos')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $todos = $em->getRepository(Todo::class)->findAll();
        foreach($todos as $todo) {
            $output->writeln($todo->__toString());
        }
    }
}
