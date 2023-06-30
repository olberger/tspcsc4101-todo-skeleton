<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use App\Repository\TodoRepository;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command Todo
 */
#[AsCommand(
    name: 'app:del-todo',
    description: 'Delete one todo',
    )]
class DelTodoCommand extends Command
{    
    /**
     *  @var TodoRepository data access repository
     */
    private $todoRepository;
    
    /**
     * Plugs the database to the command
     *
     * @param ManagerRegistry $doctrineManager
     */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->todoRepository = $doctrineManager->getRepository(Todo::class);
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to delete one todo')
        ->addArgument('todoId', InputArgument::REQUIRED, 'The id of the todo.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $id = $input->getArgument('todoId');
        
        $todo = $this->todoRepository->find($id);
        
        if ($todo) {
            $this->todoRepository->remove($todo, true);
            $io->text('Deletion completed.');
            
            return Command::SUCCESS;
        } else {
            $io->error('no todos found with id "'. $id .'"!');
            return Command::FAILURE;
        }
    }
}
