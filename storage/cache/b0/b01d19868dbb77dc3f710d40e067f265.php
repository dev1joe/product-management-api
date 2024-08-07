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

/* /forms/createProduct.twig */
class __TwigTemplate_f50462d0b419e10822580e784c994ab0 extends Template
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
    <title>Create Product</title>
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
        input[type=\"text\"], input[type=\"number\"], select {
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
    <h1>Create Product</h1>
    <form action=\"/admin/product\" method=\"POST\">
        <input type=\"text\" name=\"name\" placeholder=\"Product Name\" required><br>

        <select name=\"category\" required>
            <option value=\"\" selected disabled>-- select category --</option>

            ";
        // line 56
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["categories"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 57
            yield "                <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 57), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 57), "html", null, true);
            yield "</option>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 59
        yield "
        </select><br>

        <input name=\"price\" type=\"number\" placeholder=\"Product Price in US Dollars\"><br>

        <input name=\"photo\" type=\"text\" value=\"/temp/path/for/now\"><br>

        <textarea name=\"description\" cols=\"30\" rows=\"8\" maxlength=\"1000\">Product Description.....</textarea>
        <br>

        <br><input type=\"submit\" value=\"Submit\">
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
        return "/forms/createProduct.twig";
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
        return new Source("", "/forms/createProduct.twig", "/home/joe/PhpstormProjects/ecommerce/resources/views/forms/createProduct.twig");
    }
}
