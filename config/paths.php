# Configuração para hospedagem com caminho relativo

# Se a sua hospedagem estiver em: example.com/nr1/
# Mude BASE_PATH para: /nr1/

# Se estiver em raiz: example.com/
# Deixe: /

define('BASE_PATH', '/'); # ← ALTERE CONFORME SUA HOSPEDAGEM

# Exemplo:
# - Se URL é: https://seusite.com/nr1/login.php → use '/nr1/'
# - Se URL é: https://seusite.com/login.php → use '/'
# - Se URL é: https://nr1.seusite.com/login.php → use '/'
