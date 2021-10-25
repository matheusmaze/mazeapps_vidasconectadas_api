<div style="background-color: #efefef;font-family: sans-serif;">
    <table cellspacing="0" cellpadding="10" border="0" width="600" style="margin: 0 auto;background-color: white;border-bottom: 2px solid #84344b">
        <tr><td align="center"><img style="width: 200px; margin: 30px 0px;" src="https://adm.vidasconectadas.ong.br/favicon.ico" align="center"></td></tr>
        <tr>
            <td height="40" style="vertical-align: bottom;font-weight: bold;">Seja bem-vindo(a) {{$content['nome_usuario']}}.</td>
        </tr>
        <tr>
            <td height="70" style="vertical-align: middle;padding: 15px 40px;">
                Obrigado por se cadastrar no <strong>Vidas Conectadas!</strong><br>
                Confira abaixo os dados de acesso no nosso site:<br>

                <b>Link de acesso:</b> <a href="http://vidasconectadas.com.br/login" target="_blank">Clique aqui</a><br>
                <b>Usuário:</b> {{$content['document']}}<br>
                <b>Senha:</b> {{$content['password']}}<br>

                <tr>
                    <td style="display: grid">
                        <a href="{{$content['url']}}" style="margin:auto;"><button style = "border: none; color:white;font-weight:bold;background-color: #3efef5; padding: 15px 32px; text-align: center; text-decoration: none; font-size: 16px; width:min-content">Confirmar Email</button></a>
                    </td>
                </tr>

                Para acessar seu perfil e verificar suas cobranças e todos detalhes referente ao seu cadastro, <a href="http://vidasconectadas.com.br/login" target="_blank">clique aqui.</a><br>
                Qualquer dúvida não hesite em nos contatar!<br><br>

                <strong>Att</strong>

            </td>
        </tr>
        <tr>
            <td height="70" style="vertical-align: middle;padding: 15px 40px; font-size: 0.8em; text-align: center">
                Não responda este e-mail, em caso de dúvidas entre em contato conosco através do email: contato@mazeapps.com.br
            </td>
        </tr>
    </table>
</div>
