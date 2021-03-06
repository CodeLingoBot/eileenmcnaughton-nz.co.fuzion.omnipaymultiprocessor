<?php

namespace spec\Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Message\RequestMatcher;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestMatcherPluginSpec extends ObjectBehavior
{
    function let(RequestMatcher $requestMatcher, Plugin $plugin)
    {
        $this->beConstructedWith($requestMatcher, $plugin);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Client\Common\Plugin\RequestMatcherPlugin');
    }

    function it_is_a_plugin()
    {
        $this->shouldImplement('Http\Client\Common\Plugin');
    }

    function it_matches_a_request_and_delegates_to_plugin(
        RequestInterface $request,
        RequestMatcher $requestMatcher,
        Plugin $plugin
    ) {
        $requestMatcher->matches($request)->willReturn(true);
        $plugin->handleRequest($request, Argument::type('callable'), Argument::type('callable'))->shouldBeCalled();

        $this->handleRequest($request, function () {}, function () {});
    }

    function it_does_not_match_a_request(
        RequestInterface $request,
        RequestMatcher $requestMatcher,
        Plugin $plugin,
        Promise $promise
    ) {
        $requestMatcher->matches($request)->willReturn(false);
        $plugin->handleRequest($request, Argument::type('callable'), Argument::type('callable'))->shouldNotBeCalled();

        $next = function (RequestInterface $request) use($promise) {
            return $promise->getWrappedObject();
        };

        $this->handleRequest($request, $next, function () {})->shouldReturn($promise);
    }
}
