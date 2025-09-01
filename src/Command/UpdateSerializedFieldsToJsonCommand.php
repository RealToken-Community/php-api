<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:update-serialized-fields-to-json',
    description: 'Convert serialized fields to JSON in tokens table.',
)]
class UpdateSerializedFieldsToJsonCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fieldsToConvert = [
            'coordinate',
            'image_link',
            'secondary_marketplace',
            'secondary_marketplaces',
            'blockchain_addresses',
            'origin_secondary_marketplaces',
        ];

        $selectFields = implode(', ', array_merge(['id'], $fieldsToConvert));
        $rows = $this->connection->fetchAllAssociative("SELECT $selectFields FROM tokens");

        foreach ($rows as $row) {
            $id = $row['id'];
            $updates = [];

            foreach ($fieldsToConvert as $field) {
                $value = $row[$field];

                if (is_string($value) && str_starts_with($value, 'a:')) {
                    try {
                        $data = @unserialize($value);
                        if (is_array($data) || is_object($data)) {
                            $updates[$field] = json_encode($data, JSON_THROW_ON_ERROR);
                        } else {
                            $output->writeln("⚠️ ID $id : champ $field ne contient pas un tableau/objet");
                        }
                    } catch (Throwable $e) {
                        $output->writeln("❌ ID $id : erreur unserialize dans $field — " . $e->getMessage());
                    }
                }
            }

            if (!empty($updates)) {
                $this->connection->update('tokens', $updates, ['id' => $id]);
                $output->writeln("✅ ID $id mis à jour (" . implode(', ', array_keys($updates)) . ")");
            }
        }

        $output->writeln("🎉 Mise à jour terminée pour tous les champs.");

        return Command::SUCCESS;
    }
}
