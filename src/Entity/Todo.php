<?php
namespace App\Entity;

//use Doctrine\ORM\Mapping as ORM;
//use DateTime;
use App\Repository\TodoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tâche
 */
#[ORM\Entity(repositoryClass: TodoRepository::class)]
class Todo {
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    /**
     * @var string Description de la tâche
     *
     * La description plutôt complète de la tâche
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = "";
    
    /**
     * @var bool Is the task completed/finished.
     * 
     *  If a todo task is completed, true. If it's still active, false
     */
    #[ORM\Column]
    private ?bool $completed = null;
    
    /**
     * @var \Datetime Date of creation
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created = null;
    
    /**
     * @var \Datetime Date of last modification
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated = null;
    
    public function __construct() 
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }
    
    /**
     * @return string
     */
    public function __toString() 
    {
        $s = '';
        $s .= $this->getId() .' '. $this->getTitle() .' ';
        $s .= $this->isCompleted() ? '(completed)': '(not complete)';
        return $s;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle(?string $title): static
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function isCompleted(): ?bool
    {
        return $this->completed;
    }
    
    public function setCompleted(bool $completed): static
    {
        $this->completed = $completed;
        
        return $this;
    }
    
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }
    
    public function setCreated(?\DateTimeInterface $created): static
    {
        $this->created = $created;
        
        return $this;
    }
    
    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }
    
    public function setUpdated(?\DateTimeInterface $updated): static
    {
        $this->updated = $updated;
        
        return $this;
    }

}