<?php
namespace AmadeusService\Console\Command;

use AmadeusService\Application\BusinessCaseProvider;
use Silex\ControllerCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\GenericTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;

/**
 * Class AddEndpoint
 * @package AmadeusService\Console\Command
 */
class AddEndpoint extends Command
{
    protected function configure()
    {
        $this
            ->setName('prototype:add-endpoint')
            ->setDescription('Add a new endpoint setup.')
            ->setHelp('This command will create a new endpoint directory with an endpoint provider.')
            ->addArgument('endpoint', InputArgument::REQUIRED, 'The name of the endpoint');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endpoint = $input->getArgument('endpoint');
        $studlyCaps = ucwords($endpoint);

        $finder = new Finder();
        $finder->directories()->in(getcwd() . '/src/')->depth(0);

        /** @var SplFileInfo $directory */
        foreach ($finder as $directory) {
            if ($directory->getBasename() === $studlyCaps) {
                $output->writeln("<error>Endpoint already exists.</error>");
                exit;
            }
        }

        $fs = new Filesystem();
        $output->writeln("<comment>Creating endpoint...</comment>");
        $path = './src/' . $studlyCaps;
        $fs->mkdir(getcwd() . '/' . $path);
        $output->writeln("<info>Created <comment>$path</comment></info>");
        $fs->mkdir(getcwd() . '/' . $path . '/BusinessCase');
        $output->writeln("<info>Created <comment>$path/BusinessCase</comment></info>");

        $classGenerator = new ClassGenerator();
        $classGenerator->setNamespaceName('AmadeusService\\' . $studlyCaps);
        $classGenerator->setDocBlock(
            DocBlockGenerator::fromArray(
                [
                    'shortDescription' => "Class $studlyCaps Provider",
                    'tags' => [
                        [
                            'name' => "package",
                            'description' => "AmadeusService\\$studlyCaps"
                        ]
                    ]
                ]
            )
        );
        $classGenerator->setName($studlyCaps . 'Provider');
        $classGenerator->addUse(BusinessCaseProvider::class);
        $classGenerator->setExtendedClass(BusinessCaseProvider::class);
        $classGenerator->addMethod(
            'routing',
            [
                [
                    'name' => 'collection',
                    'type' => ControllerCollection::class
                ]
            ],
            MethodGenerator::FLAG_PUBLIC,
            "// here goes the route definition",
            DocBlockGenerator::fromArray(
                [
                    'shortDescription' => 'Method to setup the routing for the endpoint.',
                    'tags' => [
                        new GenericTag('inheritdoc', null)
                    ]
                ]
            )
        );

        $provider = $classGenerator->generate();

        $filePath = $path . '/' . $studlyCaps . 'Provider.php';
        $fs->dumpFile(getcwd() . '/' . $filePath, "<?php\n" . $provider);
        $output->writeln("<info>Created <comment>$filePath</comment></info>");
        $output->writeln("");
        $output->writeln("<info>Please register your new endpoint in <comment>./web/index.php</comment></info>");
        $lowercase = strtolower($studlyCaps);
        $providerName = $studlyCaps . 'Provider';
        $output->writeln("<info>Like <comment>\$app->mount('/$lowercase', new \AmadeusService\\$studlyCaps\\$providerName());</info>");
    }
}