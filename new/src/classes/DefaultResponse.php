<?php
    namespace app;

    use app\JsonResponse;

    class DefaultResponse {
        public static function _404EndpointNotFound() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage('Endpoint not found');
            $response->send();
            exit;
        }

        public static function _405RequestMethodNotAllowed() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(405);
            $response->setSuccess(false);
            $response->addMessage('Request method not allowed');
            $response->send();
            exit;
        }

        public static function _401NotAuthorized() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage('Not Authorized');
            $response->send();
            exit;
        }

        public static function _400NotJson() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Content-Type header is not Json');
            $response->send();
            exit;
        }

        public static function _400NotValidJson() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Content is not valid Json');
            $response->send();
            exit;
        }

        public static function _400OverLengthString($lstr = '1-100') {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("There is a string length from {$lstr}");
            $response->send();
            exit;
        }

        public static function _401UnameOrPwdNotValid() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Username or password invalid");
            $response->send();
            exit;
        }

        public static function _404NotFound($what = '') {
            $response = new JsonResponse();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("{$what} Not found");
            $response->send();
            exit;
        }

        public static function _404PageNotFound() {
            $response = new JsonResponse();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("Page not found");
            $response->send();
            exit;
        }
    }