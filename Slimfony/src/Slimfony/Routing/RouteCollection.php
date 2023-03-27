<?php

namespace Slimfony\Routing;

use Slimfony\HttpFoundation\Request;

class RouteCollection
{
    public function matchRequest(Request $request): ?Route
    {
        // TODO: match all routes paths who would fit, this would have to be done with regex
        // TODO: @see https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/Routing/Matcher/UrlMatcher.php
        // TODO: match right method
        // TODO: now only one should match, else pick first one (no throw, because config could be:
        //          /excluded/show  # this has priority over dynamic route, because defined before dynamic route
        //          /{name}/show
        //       )
        // TODO: if anything does not match at all, we return null
    }
}
