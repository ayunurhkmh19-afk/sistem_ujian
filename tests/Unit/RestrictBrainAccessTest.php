<?php

namespace Tests\Unit;

use App\Http\Middleware\RestrictBrainAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RestrictBrainAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_login_page_when_accessing_laravel_brain()
    {
        $middleware = new RestrictBrainAccess();
        $request = Request::create('/_laravel-brain', 'GET');

        Auth::shouldReceive('check')->once()->andReturn(false);

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertTrue($response->isRedirect());
        $this->assertEquals(route('login'), $response->headers->get('Location'));
    }

    public function test_user_with_role_pengawas_is_forbidden()
    {
        $middleware = new RestrictBrainAccess();
        $request = Request::create('/_laravel-brain', 'GET');

        $user = new User(['role' => 'pengawas']);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');

        $middleware->handle($request, function () {
            return new Response('OK');
        });
    }

    public function test_user_with_role_panitia_is_allowed()
    {
        $middleware = new RestrictBrainAccess();
        $request = Request::create('/_laravel-brain', 'GET');

        $user = new User(['role' => 'panitia']);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_other_paths_are_untouched()
    {
        $middleware = new RestrictBrainAccess();
        $request = Request::create('/dashboard', 'GET');

        // Check should NOT be called for other paths
        Auth::shouldReceive('check')->never();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }
}
