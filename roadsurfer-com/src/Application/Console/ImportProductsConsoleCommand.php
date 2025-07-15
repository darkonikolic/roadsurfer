<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Application\Service\ImportProductsService;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from JSON file'
)]
class ImportProductsConsoleCommand extends Command
{
    public function __construct(
        private ImportProductsService $importProductsService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to JSON file with products'
            )
            ->setHelp('This command imports products from a JSON file into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        $io->title('Importing Products');
        $io->text("Reading file: {$filePath}");

        try {
            $this->validateFile($filePath, $io);
            $importResult = $this->importProductsService->importFromFile($filePath);
            $this->displayResults($importResult, $io);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Import failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function validateFile(string $filePath, SymfonyStyle $io): void
    {
        if (!file_exists($filePath)) {
            $io->error("File not found: {$filePath}");
            $io->text('Please provide a valid path to a JSON file.');
            $io->text('Example: php bin/console app:import-products request.json');
            throw new InvalidArgumentException("File not found: {$filePath}");
        }
    }

    /**
     * @param array{importedCount: int, errors: array<string>} $importResult
     */
    private function displayResults(array $importResult, SymfonyStyle $io): void
    {
        $importedCount = $importResult['importedCount'];
        $errors        = $importResult['errors'];

        if (empty($errors)) {
            $io->success("Successfully imported {$importedCount} products!");
            return;
        }

        $io->warning('Import completed with errors:');
        $io->text("Imported: {$importedCount} products");
        $io->text('Errors: ' . count($errors));

        foreach ($errors as $error) {
            $io->text("  - {$error}");
        }
    }

}
