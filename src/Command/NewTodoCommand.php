<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Todo
 */
class NewTodoCommand extends Command
{    
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:new-todo')
        
        // the short description shown while running "php bin/console list"
        ->setDescription('Creates a new todo.')
        
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to list one todo')
        ->addArgument('title', InputArgument::REQUIRED, 'The title of the todo.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $todo = new Todo();
        $todo->setTitle($input->getArgument('title'));
        $todo->setCompleted(false);
        $em = $this->entityManager;
        $em->persist($todo);
        $em->flush();
        $output->writeln('Created: '. $todo);
        return 0;
    }
}
