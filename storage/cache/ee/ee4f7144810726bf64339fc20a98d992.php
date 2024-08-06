<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* /forms/createWarehouse.twig */
class __TwigTemplate_69e2d36b61531bce3c8df4543a39b6e6 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Create Warehouse</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .form-container {
            // text-align: center;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
        input[type=\"text\"], select {
            padding: 10px;
            width: -webkit-fill-available;
            max-width: 300px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type=\"submit\"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type=\"submit\"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class=\"form-container\">
    <h1>Create Warehouse</h1>
    <form action=\"/admin/warehouse\" method=\"POST\">
        <input type=\"text\" name=\"name\" placeholder=\"Warehouse Name\" required>

        <h3>Select and existing address</h3>
        <select name=\"address_id\">
           <option value='' selected disabled>-- select address --</option>
            ";
        // line 56
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["addresses"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["address"]) {
            // line 57
            yield "                <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["address"], "id", [], "any", false, false, false, 57), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["address"], "details", [], "any", false, false, false, 57), "html", null, true);
            yield "</option>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['address'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 59
        yield "
        </select>

        <h3>or add a new address</h3>
        <select name=\"country\">
            <option value=\"\" selected disabled>-- select country --</option>
            <option value=\"Egypt\">Egypt</option>
            <option value=\"UAE\">United Arab Emirates</option>
            <option value=\"KSA\">Saudi Arabia</option>
            <option value=\"Morocco\">Morocco</option>
        </select><br>

        <input type=\"text\" name=\"governorate\" placeholder=\"Governorate / State\"><br>
        <input type=\"text\" name=\"district\" placeholder=\"District / City\"><br>
        <input type=\"text\" name=\"street\" placeholder=\"Street\"><br>
        <input type=\"text\" name=\"building\" placeholder=\"Building\"><br>

        <input type=\"submit\" value=\"Submit\">
    </form>
</div>
</body>
</html>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "/forms/createWarehouse.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  110 => 59,  99 => 57,  95 => 56,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/forms/createWarehouse.twig", "/home/joe/PhpstormProjects/ecommerce/resources/views/forms/createWarehouse.twig");
    }
}
