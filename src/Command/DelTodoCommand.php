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
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Todo
 */
class DelTodoCommand extends Command
{    
    private $doctrineManager;
    
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->doctrineManager = $doctrineManager;
        
        parent::__construct();
    }
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:del-todo')
        
        // the short description shown while running "php bin/console list"
        ->setDescription('Delete one todo.')
        
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to delete one todo')
        ->addArgument('todoId', InputArgument::REQUIRED, 'The id of the todo.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        
        $id = $input->getArgument('todoId');
        
        $em = $this->doctrineManager;
        $todo = $em->getRepository(Todo::class)->find($id);
        
        if ($todo) {
            $em->remove($todo);
            $em->flush();
        } else {
            $errOutput->writeln('<error>no todos found with id "'. $id .'"!</error>');
            return 1;
        }
        return 0;
    }
}
