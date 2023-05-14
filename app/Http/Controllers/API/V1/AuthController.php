<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\AuthRepository;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     description="API Documentation - ToDo",
 *     version="1.0.0",
 *     title="ToDO API Documentation",
 *
 *     @OA\Contact(
 *         email="simonishah63@gmail.com"
 *     ),
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OAS\SecurityScheme(
 *      securityScheme="sanctum",
 *      name="Authorization",
 *      type="http",
 *      scheme="bearer",
 *      in="header",
 * )
 *
 * @OA\Tag(
 *     name="ToDo Api",
 *     description="API Endpoints of todo Api"
 * )
 */
class AuthController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Auth related functionalities.
     *
     * @var AuthRepository
     */
    public $authRepository;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login",
     *     description="Login by email, password",
     *     operationId="login",
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              type="object",
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", minLength=6, example="User@123")
     *          ),
     *      ),
     *
     *      @OA\Response(response=200, description="Logged In Successfully!"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=401, description="The provided credentials are incorrect."),
     *      @OA\Response(response=422, description="The given data was invalid."),
     * )
     */
    public function Login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if ($this->guard()->attempt($credentials)) {
            $token = $this->guard()->user()->createToken('access_token')->plainTextToken;
            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];

            return $this->responseSuccess($data, 'Logged In Successfully!');
        } else {
            return $this->responseError('Unauthorised.', 'The provided credentials are incorrect.', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/v1/register",
     *     tags={"Authentication"},
     *     summary="Register User",
     *     description="Register New User",
     *     operationId="register",
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              type="object",
     *
     *              @OA\Property(property="name", type="string", maxLength=50, example="Jhon Doe"),
     *              @OA\Property(property="email", type="string", format="email", maxLength=255, example="jhondoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", minLength=6, example="123456"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="123456")
     *          ),
     *      ),
     *
     *      @OA\Response(response=200, description="User Registered and Logged in Successfully!"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Content")
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->only('name', 'email', 'password', 'password_confirmation');
            $user = $this->authRepository->register($data);
            if ($user) {
                event(new Registered($user));
                $token = $user->createToken('access_token')->plainTextToken;
                $data = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ];

                return $this->responseSuccess($data, 'User Registered and Logged in Successfully!', Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->responseError('Something went wrong.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Logout",
     *     operationId="logout",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          description="application/json",
     *          example="application/json",
     *
     *          @OA\Schema(
     *              type="string"
     *           )
     *     ),
     *
     *     @OA\Response(response=200, description="Logout"),
     *     @OA\Response(response=401, description="Returns when user is not authenticated",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     *
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            $this->guard()->logout();

            return $this->responseSuccess(null, 'Logged out successfully!');
        } catch (\Exception $e) {
            return $this->responseError('Something went wrong.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
