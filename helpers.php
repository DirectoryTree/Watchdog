<?php

if (! function_exists('form')) {
    /**
     * Create a new form input factory.
     *
     * @return \DirectoryTree\Watchdog\Http\Html\FormFactory
     */
    function form()
    {
        return new \DirectoryTree\Watchdog\Http\Html\FormFactory();
    }
}

if (! function_exists('route_filter')) {
    /**
     * Generates a URL to the specific route including the parameters and additional query params.
     *
     * @param string $name       The route name.
     * @param array  $parameters The route parameters.
     * @param array  $additional The additional query parameters.
     *
     * @return string
     */
    function route_filter($name, array $parameters = [], array $additional = [])
    {
        $query = array_merge(request()->query(), $additional);

        return route($name, array_merge($parameters, $query));
    }
}

if (! function_exists('current_route_filter')) {
    /**
     * Generates a URL to the specific route including the parameters and additional query params.
     *
     * @param array $additional The additional query parameters.
     *
     * @return string
     */
    function current_route_filter(array $additional = [])
    {
        $route = request()->route();

        return route_filter($route->getName(), $route->parameters(), $additional);
    }
}
