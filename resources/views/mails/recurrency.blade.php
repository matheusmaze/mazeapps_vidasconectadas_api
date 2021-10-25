@component('mail::message')
# Introduction

Olá, <strong>{{$name}}</strong>!<br>
Obrigado por fazer parte dos nossos doadores! <br><br>
<strong>{{$thanks}}</strong>

Aqui está o código para realizar a sua próxima doação.<br>
<strong>Valor:</strong> {{$value}} <br>
Código Pix copia e cola: <strong>{{$pix}}</strong><br>

@component('mail::button', ['url' => ''])
Copiar Código
@endcomponent

<strong>Atenção!</strong><br>
Na hora de realizar o pagamento, depois de colar o código Pix, confira as seguintes informações. <br>
<strong>Nome:</strong> Mazeapps Desenvolvimento de Software Ltda <br>
<strong>CNPJ:</strong> XX.XXX.XXX / XXX1-XX <br>
<strong>Banco:</strong> GerenciaNet S.A <br>
<strong>Chave:</strong> contatomazeapps@gmail.com <br><br>

Atenciosamente,<br>
Equipe Vidas Conectadas.
@endcomponent
