<?php
namespace AmadeusService\Console\Command;

use AmadeusService\Application\BusinessCase;
use AmadeusService\Application\Response\HalResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Reflection\ClassReflection;

/**
 * Class AddBusinessCase
 * @package AmadeusService\Console\Command
 */
class AddBusinessCase extends Command
{
    protected function configure()
    {
        $this
            ->setName('prototype:add-business-case')
            ->setDescription('Add a new business-case.')
            ->setHelp('This command will create an business case with name in the selected endpoint.')
            ->addArgument('business-case', InputArgument::REQUIRED, 'The name of the business-case');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $businessCase = $input->getArgument('business-case');
        $studlyCaps = ucwords($businessCase);

        $finder = new Finder();
        $finder->directories()->in(getcwd() . '/src/')->depth(0);
        $endpoints = [];
        /** @var SplFileInfo $endpoint */
        foreach ($finder as $endpoint) {
            array_push($endpoints, $endpoint->getBasename());
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select the endpoint the business case should be created for.',
            $endpoints,
            0
        );
        $question->setErrorMessage('Endpoint does not exist.');
        $endpoint = $helper->ask($input, $output, $question);

        $finder = new Finder();
        $finder->files()->in(getcwd() . '/src/' . $endpoint . '/BusinessCase/')->depth(0);

        /** @var SplFileInfo $businessCase */
        foreach ($finder as $businessCase) {
            echo $businessCase . "\n";
            if ($businessCase->getBasename() === $studlyCaps) {
                $output->writeln("<error>Business case already exists.</error>");
                exit;
            }
        }

        $fs = new Filesystem();
        $output->writeln("<comment>Creating business case...</comment>");
        $endpointProviderPath = './src/' . $endpoint . '/' . $endpoint . 'Provider.php';
        $businessCasePath = './src/' . $endpoint . '/BusinessCase/' . $studlyCaps . '.php';

        $classGenerator = new ClassGenerator();
        $classGenerator->setNamespaceName('AmadeusService\\' . $endpoint . '\BusinessCase');
        $classGenerator->setDocBlock(
            DocBlockGenerator::fromArray(
                [
                    'shortDescription' => "Class $studlyCaps BusinessCase",
                    'tags' => [
                        [
                            'name' => "package",
                            'description' => "AmadeusService\\$endpoint\BusinessCase"
                        ]
                    ]
                ]
            )
        );
        $classGenerator->setName($studlyCaps);
        $classGenerator->addUse(BusinessCase::class);
        $classGenerator->addUse(HalResponse::class);
        $classGenerator->setExtendedClass(BusinessCase::class);
        $classGenerator->addMethod(
            'respond',
            [],
            MethodGenerator::FLAG_PUBLIC,
            "return new HalResponse(null, 200);",
            DocBlockGenerator::fromArray(
                [
                    'shortDescription' => 'Method to define what the business case returns.',
                    'tags' => [
                        new ReturnTag('HalResponse')
                    ]
                ]
            )
        );

        $provider = $classGenerator->generate();

        $fs->dumpFile(getcwd() . '/' . $businessCasePath, "<?php\n" . $provider);
        $output->writeln("<info>Created <comment>$businessCasePath</comment></info>");

        $providerName = $endpoint . 'Provider';
        $class = ClassGenerator::fromReflection(
            new ClassReflection("AmadeusService\\$endpoint\\$providerName")
        );

        $method = $class->getMethod('routing');
        $currentBody = $method->getBody();
        $currentBody .= "\n" . "\$collection->match('/', \AmadeusService\\$endpoint\BusinessCase\\$studlyCaps::class);";
        $class->getMethod('routing')->setBody($currentBody);

        $fs->dumpFile(getcwd() . '/' . $endpointProviderPath, "<?php\n" . $class->generate());
        $output->writeln("<info>Updated <comment>$endpointProviderPath</comment></info>");
    }
}