<?php


namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

trait DateTimeTrait
{
    #[ORM\Column(nullable: true)]
    private \DateTime $date_created;
    #[ORM\Column(nullable: true)]
    private \DateTime $date_modified;

    /**
     * DateTimeTrait constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->date_created=new \DateTime('now',new \DateTimeZone('Africa/Brazzaville'));
        $this->date_modified=new \DateTime('now',new \DateTimeZone('Africa/Brazzaville'));
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param mixed $date_created
     */
    public function setDateCreated($date_created): void
    {
        $this->date_created = $date_created;
    }

    /**
     * @return mixed
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * @param mixed $date_modified
     */
    public function setDateModified($date_modified): void
    {
        $this->date_modified = $date_modified;
    }


}
