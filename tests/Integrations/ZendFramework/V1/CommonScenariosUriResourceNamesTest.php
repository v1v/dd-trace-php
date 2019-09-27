<?php

namespace DDTrace\Tests\Integrations\ZendFramework\V1;

use DDTrace\Tests\Common\SpanAssertion;
use DDTrace\Tests\Common\WebFrameworkTestCase;
use DDTrace\Tests\Frameworks\Util\Request\RequestSpec;

final class CommonScenariosUriResourceNamesTest extends WebFrameworkTestCase
{
    protected static function getAppIndexScript()
    {
        return __DIR__ . '/../../../Frameworks/ZendFramework/Version_1_12/public/index.php';
    }

    protected static function getEnvs()
    {
        return array_merge(parent::getEnvs(), [
            'DD_SERVICE_NAME' => 'zf1_uri_resource_names',
            'DD_TRACE_URL_AS_RESOURCE_NAMES_ENABLED' => 'true',
        ]);
    }

    /**
     * @dataProvider provideSpecs
     * @param RequestSpec $spec
     * @param array $spanExpectations
     * @throws \Exception
     */
    public function testScenario(RequestSpec $spec, array $spanExpectations)
    {
        $traces = $this->tracesFromWebRequest(function () use ($spec) {
            $this->call($spec);
        });

        $this->assertExpectedSpans($traces, $spanExpectations);
    }

    public function provideSpecs()
    {
        return $this->buildDataProvider(
            [
                'A simple GET request returning a string' => [
                    SpanAssertion::build(
                        'zf1.request',
                        'zf1_uri_resource_names',
                        'web',
                        'GET /simple'
                    )->withExactTags(SpanAssertion::NOT_TESTED),
                ],
                'A simple GET request with a view' => [
                    SpanAssertion::build(
                        'zf1.request',
                        'zf1_uri_resource_names',
                        'web',
                        'GET /simple_view'
                    )->withExactTags(SpanAssertion::NOT_TESTED),
                ],
                'A GET request with an exception' => [
                    SpanAssertion::build(
                        'zf1.request',
                        'zf1_uri_resource_names',
                        'web',
                        'GET /error'
                    )->withExactTags(SpanAssertion::NOT_TESTED)->setError(),
                ],
            ]
        );
    }
}
