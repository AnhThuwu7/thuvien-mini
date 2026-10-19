<?php

/**
 * Quy ước URL -> Controller -> method:
 *   /                      -> HomeController::index()
 *   /sach                  -> SachController::index()
 *   /sach/chi-tiet/5       -> SachController::chiTiet(5)
 *   /phieu-muon/giao-sach  -> PhieuMuonController::giaoSach()
 * Dấu gạch ngang (-) trong URL = ranh giới giữa các từ. Tên file phải khớp CHÍNH XÁC
 * (ví dụ PhieuMuonController.php) vì hosting Linux phân biệt hoa/thường.
 */
class Router
{
    private string $controller = 'HomeController';
    private string $method     = 'index';
    private array  $params     = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Controller (không có đoạn đầu -> HomeController)
        if (isset($url[0])) {
            if (!$this->isValidSegment($url[0])) {
                $this->notFound();
            }
            $this->controller = $this->toStudly($url[0]) . 'Controller';
            array_shift($url);
        }

        $file = __DIR__ . '/../app/Controllers/' . $this->controller . '.php';
        if (!is_file($file)) {
            $this->notFound();
        }
        require_once $file;

        if (!class_exists($this->controller, false) || !is_subclass_of($this->controller, 'Controller')) {
            $this->notFound();
        }
        $controller = new $this->controller();

        // 2. Method (không có -> index)
        $action = 'index';
        if (isset($url[0])) {
            if (!$this->isValidSegment($url[0])) {
                $this->notFound();
            }
            $action = lcfirst($this->toStudly($url[0]));
            array_shift($url);
        }
        if (!$this->isRoutable($controller, $action)) {
            $this->notFound();
        }
        $this->method = $action;

        // 3. Tham số còn lại
        $this->params = $url;
        $this->prepareParams($controller);

        call_user_func_array([$controller, $this->method], $this->params);
    }

    private function parseUrl(): array
    {
        $url = $_GET['url'] ?? '';
        if (!is_string($url)) {          // ?url[]=... -> không hợp lệ
            $this->notFound();
        }
        $url = trim($url, '/');
        if ($url === '') {
            return [];
        }
        return array_values(array_filter(explode('/', $url), fn($s) => $s !== ''));
    }

    // Đoạn dùng làm tên controller/method: chữ cái, số, dấu gạch ngang
    private function isValidSegment(string $segment): bool
    {
        return (bool) preg_match('/^[A-Za-z][A-Za-z0-9-]*$/', $segment);
    }

    // phieu-muon -> PhieuMuon
    private function toStudly(string $segment): string
    {
        return str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $segment))));
    }

    private function isRoutable(object $controller, string $action): bool
    {
        if (!method_exists($controller, $action)) {
            return false;
        }
        $ref = new ReflectionMethod($controller, $action);
        return $ref->isPublic()
            && !$ref->isStatic()
            && strpos($ref->name, '__') !== 0
            && $ref->getDeclaringClass()->getName() !== 'Controller';
    }

    // Kiểm tra số lượng tham số; tham số khai báo kiểu int phải là số nguyên không âm (ép kiểu giúp luôn)
    private function prepareParams(object $controller): void
    {
        $ref   = new ReflectionMethod($controller, $this->method);
        $count = count($this->params);

        if ($count < $ref->getNumberOfRequiredParameters()
            || (!$ref->isVariadic() && $count > $ref->getNumberOfParameters())) {
            $this->notFound();
        }

        foreach ($ref->getParameters() as $i => $param) {
            if (!array_key_exists($i, $this->params)) {
                continue;
            }
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && $type->getName() === 'int') {
                if (!ctype_digit($this->params[$i])) {
                    $this->notFound();
                }
                $this->params[$i] = (int) $this->params[$i];
            }
        }
    }

    private function notFound(): void
    {
        abort(404);
    }
}
