<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\ImageTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200414145453 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add property `type` to table `koi_image`';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_image ADD type VARCHAR(255)');
        $this->addSql("UPDATE koi_image SET type = CASE WHEN thumbnail_path ISNULL THEN '". ImageTypeEnum::TYPE_AVATAR . "' ELSE '" . ImageTypeEnum::TYPE_COMMON . "'END;");
        $this->addSql('ALTER TABLE koi_image ALTER COLUMN type SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
