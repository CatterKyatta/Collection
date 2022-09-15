<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220915152114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `children_display_mode` property to `koi_album`, `koi_collection` and `koi_wishlist`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_album SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_album ALTER COLUMN children_display_mode SET NOT NULL');

        $this->addSql('ALTER TABLE koi_collection ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_collection SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_collection ALTER COLUMN children_display_mode SET NOT NULL');

        $this->addSql('ALTER TABLE koi_wishlist ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_wishlist SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_wishlist ALTER COLUMN children_display_mode SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
