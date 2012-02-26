<?php

/**
 * This is a helperfile for IDE code completion, it's never actually used
 */

namespace Classes\Application
{
	abstract class Base extends \Fuel\Kernel\Application\Base {}
}

namespace Classes\Controller
{
	class Base extends \Fuel\Kernel\Controller\Base {}

	class Template extends \Fuel\Core\Controller\Template {}
}

namespace Classes\Data
{
	abstract class Base extends \Fuel\Kernel\Data\Base {}
	class Config extends \Fuel\Kernel\Data\Config {}
	class Language extends \Fuel\Kernel\Data\Language {}
}

namespace Classes\DiC
{
	class Base extends \Fuel\Kernel\DiC\Base {}
}

namespace Classes\Loader
{
	interface Loadable extends \Fuel\Kernel\Loader\Loadable {}
	class Package extends \Fuel\Kernel\Loader\Package {}
}

namespace Classes\Presenter
{
	abstract class Presenter extends \Fuel\Kernel\Presenter\Base {}
}

namespace Classes\Request
{
	abstract class Base extends \Fuel\Kernel\Request\Base {}
	class Fuel extends \Fuel\Kernel\Request\Fuel {}

	class Curl extends \Fuel\Core\Request\Curl {}
}

namespace Classes\Response
{
	class Base extends \Fuel\Kernel\Response\Base {}
}

namespace Classes\Route
{
	abstract class Base extends \Fuel\Kernel\Route\Base {}
	class Fuel extends \Fuel\Kernel\Route\Fuel {}

	class Task extends \Fuel\Core\Route\Task {}
}

namespace Classes\Task
{
	class Base extends \Fuel\Core\Task\Base {}
}

namespace Classes\View
{
	class View extends \Fuel\Kernel\View\Base {}
}
