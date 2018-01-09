<?php

namespace Swoft\Testing\Base;

use PHPUnit\Framework\Assert as PHPUnit;
use Swoft\Helper\StringHelper;

/**
 * @uses      ResponseAssertTrait
 * @version   2017-12-06
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
trait ResponseAssertTrait
{

    /**
     * Assert that the response has a successful status code.
     *
     * @return $this
     */
    public function assertSuccessful()
    {
        PHPUnit::assertTrue($this->isSuccessful(), 'Response status code [' . $this->getStatusCode() . '] is not a successful status code.');

        return $this;
    }

    /**
     * Assert that the response has the given status code.
     *
     * @param  int $status
     * @return $this
     */
    public function assertStatus($status)
    {
        $actual = $this->getStatusCode();

        PHPUnit::assertTrue($actual === $status, "Expected status code {$status} but received {$actual}.");

        return $this;
    }

    /**
     * Asserts that the response contains the given header and equals the optional value.
     *
     * @param  string $headerName
     * @param  mixed  $value
     * @return $this
     */
    public function assertHeader($headerName, $value = null)
    {
        PHPUnit::assertTrue($this->hasHeader($headerName), "Header [{$headerName}] not present on response.");

        $actual = $this->getHeaderLine($headerName);

        if (! is_null($value)) {
            PHPUnit::assertEquals($value, $this->getHeaderLine($headerName), "Header [{$headerName}] was found, but value [{$actual}] does not match [{$value}].");
        }

        return $this;
    }

    /**
     * Assert that the given string is contained within the response.
     *
     * @param  string $value
     * @return $this
     */
    public function assertSee($value)
    {
        PHPUnit::assertContains($value, $this->getBody()->getContents());

        return $this;
    }

    /**
     * Assert that the given string is contained within the response text.
     *
     * @param  string $value
     * @return $this
     */
    public function assertSeeText($value)
    {
        PHPUnit::assertContains($value, strip_tags($this->getBody()->getContents()));

        return $this;
    }

    /**
     * Assert that the given string is not contained within the response.
     *
     * @param  string $value
     * @return $this
     */
    public function assertDontSee($value)
    {
        PHPUnit::assertNotContains($value, $this->getBody()->getContents());

        return $this;
    }

    /**
     * Assert that the given string is not contained within the response text.
     *
     * @param  string $value
     * @return $this
     */
    public function assertDontSeeText($value)
    {
        PHPUnit::assertNotContains($value, strip_tags($this->getBody()->getContents()));

        return $this;
    }

    /**
     * Assert that the response is a superset of the given JSON.
     *
     * @param  array $data
     * @return $this
     */
    public function assertJson(array $data)
    {
        PHPUnit::assertArraySubset($data, $this->decodeResponseJson(), false, $this->assertJsonMessage($data));

        return $this;
    }

    /**
     * Get the assertion message for assertJson.
     *
     * @param  array $data
     * @return string
     */
    protected function assertJsonMessage(array $data)
    {
        $expected = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $actual = json_encode($this->decodeResponseJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return 'Unable to find JSON: ' . PHP_EOL . PHP_EOL . "[{$expected}]" . PHP_EOL . PHP_EOL . 'within response JSON:' . PHP_EOL . PHP_EOL . "[{$actual}]." . PHP_EOL . PHP_EOL;
    }

    /**
     * Assert that the response has the exact given JSON.
     *
     * @param  array $data
     * @return $this
     */
    public function assertExactJson(array $data)
    {
        $actual = json_encode((array)$this->decodeResponseJson());

        PHPUnit::assertEquals(json_encode($data), $actual);

        return $this;
    }

    /**
     * Assert that the response contains the given JSON fragment.
     *
     * @param  array $data
     * @return $this
     */
    public function assertJsonFragment(array $data)
    {
        $actual = json_encode((array)$this->decodeResponseJson());

        foreach ($data as $key => $value) {
            $expected = substr(json_encode([$key => $value]), 1, -1);

            PHPUnit::assertTrue(StringHelper::contains($actual, $expected), 'Unable to find JSON fragment: ' . PHP_EOL . PHP_EOL . "[{$expected}]" . PHP_EOL . PHP_EOL . 'within' . PHP_EOL . PHP_EOL . "[{$actual}].");
        }

        return $this;
    }

    /**
     * Assert that the response does not contain the given JSON fragment.
     *
     * @param  array $data
     * @return $this
     */
    public function assertJsonMissing(array $data)
    {
        $actual = json_encode($this->decodeResponseJson());

        foreach ($data as $key => $value) {
            $expected = substr(json_encode([$key => $value]), 1, -1);

            PHPUnit::assertFalse(StringHelper::contains($actual, $expected), 'Found unexpected JSON fragment: ' . PHP_EOL . PHP_EOL . "[{$expected}]" . PHP_EOL . PHP_EOL . 'within' . PHP_EOL . PHP_EOL . "[{$actual}].");
        }

        return $this;
    }

    /**
     * Assert that the response has a given JSON structure.
     *
     * @param  array      $structure
     * @param  array|null $responseData
     * @return $this
     */
    public function assertJsonStructure(array $structure, $responseData = null)
    {
        if (is_null($responseData)) {
            $responseData = $this->decodeResponseJson();
        }

        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                PHPUnit::assertInternalType('array', $responseData);

                foreach ($responseData as $responseDataItem) {
                    $this->assertJsonStructure($structure['*'], $responseDataItem);
                }
            } elseif (is_array($value)) {
                PHPUnit::assertArrayHasKey($key, $responseData);

                $this->assertJsonStructure($structure[$key], $responseData[$key]);
            } else {
                PHPUnit::assertArrayHasKey($value, $responseData);
            }
        }

        return $this;
    }

    /**
     * Validate and return the decoded response JSON.
     *
     * @return array
     */
    protected function decodeResponseJson()
    {
        $decodedResponse = json_decode($this->getBody()->getContents(), true);

        if (is_null($decodedResponse) || $decodedResponse === false) {
            if ($this->exception) {
                throw $this->exception;
            } else {
                PHPUnit::fail('Invalid JSON was returned from the route.');
            }
        }

        return $decodedResponse;
    }
}
