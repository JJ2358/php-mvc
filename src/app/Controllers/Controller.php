<?php

declare(strict_types=1);

namespace App\Controllers;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

/**
 * Base controller class that initializes Twig environment and provides
 * common functionalities to child controllers.
 */
class Controller
{
    /**
     * @var FilesystemLoader Twig loader for locating the templates.
     */
    protected $loader;

    /**
     * @var Environment Twig environment for rendering templates.
     */
    protected $twig;

    /**
     * Controller constructor initializes Twig and adds global functions.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initializes the Twig environment and loader with configurations.
     * Also, calls the method to add global functions to Twig.
     */
    private function init(): void
    {
        $this->loader = new FilesystemLoader(VIEWS_PATH);
        $this->twig = new Environment($this->loader, [
            'cache' => APP_ROOT . '/twig_cache',
            'auto_reload' => true,
        ]);
        $this->addGlobalFunctions();
    }

    /**
     * Renders a Twig template with optional data.
     *
     * Merges provided data with default data and renders the specified
     * Twig layout.
     *
     * @param string $layout The Twig layout file to render.
     * @param array $data Optional data to pass to the Twig template.
     */
    public function render(string $layout, array $data = []): void
    {
        echo $this->twig->render(
            "Layouts/$layout",
            array_merge($data, ['controller' => $this])
        );
    }

    /**
     * Registers global functions in the Twig environment.
     *
     * This method adds application-specific functions that can be used
     * globally within any Twig template.
     */
    protected function addGlobalFunctions(): void
    {
        $functions = [
            'getSessionMessage',
            'getErrorMessage',
            'getField',
        ];

        foreach ($functions as $function) {
            $this->twig->addFunction(new TwigFunction($function, [$this, $function]));
        }
    }
}
