<?php
/**
 * Gestion de la commande d'affichage d'un tâche en ligne de commande
 *
 * @copyright  2017-2018 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Command ShowTodo
 */
class ShowTodoCommand extends Command
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
        ->setName('app:show-todo')
        
        // the short description shown while running "php bin/console list"
        ->setDescription('Show one todo.')
        
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to show one todo')
        ->addArgument('todoId', InputArgument::REQUIRED, 'The id of the todo.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        $id = $input->getArgument('todoId');
        $todo = $this->todoRepository->find($id);
        
        if ($todo) {
            // $output->writeln($todo->__toString());
            $output->writeln($todo);
            return 1;
        } else {
            $errOutput->writeln('<error>no todos found with id "'. $id .'"!</error>');
        }
        return 0;
    }
}
