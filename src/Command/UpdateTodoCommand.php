<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use \DateTime;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Command Todo
 */
class UpdateTodoCommand extends Command
{    
    private $doctrineManager;
    private $todoRepository;
    
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->doctrineManager = $doctrineManager;
        $this->todoRepository = $doctrineManager->getRepository(Todo::class);
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:update-todo')
        
        // the short description shown while running "php bin/console list"
        ->setDescription('Updates a todo.')
        
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to update one todo')
        ->addArgument('todoId', InputArgument::REQUIRED, 'The id of the todo.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    
        $id = $input->getArgument('todoId');
        
        $todo = $this->todoRepository->find($id);
        
        if ($todo) {
            if(! $todo->getCompleted()) {
                $todo->setCompleted(true);
                $todo->setUpdated(new \DateTime());
                $this->em->flush();
            } else {
                $output->writeln('Todo '. $id .' already completed. Nothing done.');
            }
        } else {
            $errOutput->writeln('<error>no todos found with id "'. $id .'"!</error>');
            return 1;
        }
        return 0;
    }
}
