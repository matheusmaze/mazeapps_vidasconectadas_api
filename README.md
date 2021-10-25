# PAdrinhos

Backend.

## Rotas disponíveis

Para todas as rotas o seguinte se aplica:

- Caso algum dado esteja inválido a resposta conterá o código **HTTP 422** junto de um JSON informando quais campos estão inválidos.
- Em caso de instabilidade ou falha de sistema a resposta conterá o código **HTTP 500**. _Estes casos serão extremamente raros_.

### AUTENTICAÇÃO

Para que você possa fazer uso das rotas é necessário um token de autenticação.
Isso pode ser facilmente ajustado posteriormente através de um mecanismo de política de acessos que o próprio Laravel disponibiliza.

#### Autenticar
**POST** `/api/login`
**Headers** `Accept: application/json`

```
login=meu-usuario
senha=minha-senha
```

Uma autenticação bem sucedida irá retornar um token de acesso, o nome do usuário, seu `id` e o código **HTTP 200**.

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTYyMzkzMDQ2MSwiZXhwIjoxNjIzOTM0MDYxLCJuYmYiOjE2MjM5MzA0NjEsImp0aSI6InZRTE5JNXBWTzdYWG1yR3YiLCJzdWIiOjIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEiLCJpZCI6MiwibmFtZSI6IlZpbmljaXVzIiwiZW1haWwiOiJ2aW5pY2l1c0BnbWFpbC5jb20iLCJuaXZlbCI6Ik1BU1RFUiJ9.KqMdnkeUD6NytFCT3-D3iDrxdCSKHvmPofGhlxuKfgI"
}
```

Uma autenticação falha irá retornar o código **HTTP 401** junto de um JSON.

```json
{
    "message": "Credenciais incorretas!",
    "data": "2021-06-17 11:48:54"
}
```

Agora basta adicionar o token em **todas** as requisições que precisam de autenticação:
`Authorization: Bearer <seu-jwt-token-aqui>`
