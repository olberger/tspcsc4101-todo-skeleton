<?php
/**
 * Gestion de la commande de liste des tâches actives en ligne de commande
 *
 * @copyright  2018 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Command;

use App\Entity\Todo;
use App\Repository\TodoRepository;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Command ListactiveTodos
 *
 * cf. https://symfony.com/doc/current/console.html
 *
 */
#[AsCommand(
    name: 'app:list-active-todos',
    description: 'List the active todos',
    )]
 class ListactiveTodosCommand extends Command
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
        ->setHelp("This command allows you to list the todos which are active, i.e. not yet completed")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        // récupère une liste toutes les instances de la classe Todo
        $todos = $this->todoRepository->findAll();
        
        // filtrer les tâches pas encore terminées
        $actives=array();
        foreach($todos as $todo) {
            if(! $todo->isCompleted()) {
                $actives[] = $todo;
            }
        }
        
        if(! empty($actives)) {
            $io->title('list of active todos:');
            $io->listing($actives);
        } else {
            $io->error('no todos found!');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
// Alternative basée sur Doctrine
//         // récupère une liste toutes les instances de la classe Todo dont completed vaut false
//         $todos = $this->todoRepository->findByCompleted(false);
//         if(! empty($todos)) {
//             $io->title('list of active todos:');
//             $io->listing($todos);
//             return Command::SUCCESS;
//         } else {
//             $io->error('no active todos found!');
//             return Command::FAILURE;  
//         }
    }
}
