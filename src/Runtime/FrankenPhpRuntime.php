<?php

namespace App\Runtime;

use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

/**
 * FrankenPHP Runtime avec support du mode Worker
 *
 * Le mode worker garde l'application Symfony en mémoire entre les requêtes
 * pour des performances maximales (pas de bootstrap à chaque requête)
 */
class FrankenPhpRuntime extends SymfonyRuntime
{
	public function getRunner(?object $application): RunnerInterface
	{
		if (\function_exists('frankenphp_handle_request')) {
			return new FrankenPhpRunner($application);
		}

		return parent::getRunner($application);
	}
}

class FrankenPhpRunner implements RunnerInterface
{
	private $application;

	public function __construct(object $application)
	{
		$this->application = $application;
	}

	public function run(): int
	{
		// Mode worker : boucle infinie qui traite les requêtes
		// L'application reste en mémoire entre les requêtes
		\frankenphp_handle_request(function () {
			// Reset des variables globales entre chaque requête
			$_SERVER['APP_RUNTIME_OPTIONS'] = [];

			// Traiter la requête avec l'application en mémoire
			return $this->application->handle(
				\Symfony\Component\HttpFoundation\Request::createFromGlobals()
			);
		});

		return 0;
	}
}