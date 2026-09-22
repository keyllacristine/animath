<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste do Banco - Animath</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f1e8;
            color: #333;
        }

        h1 {
            margin-bottom: 30px;
        }

        section {
            background: white;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        form {
            margin-top: 15px;
        }

        input, select, button {
            padding: 7px;
            margin: 4px;
        }

        button {
            cursor: pointer;
        }

        .success {
            padding: 10px;
            background: #dff0d8;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<h1>Animath - Teste do Banco de Dados</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="success">
        Operação realizada com sucesso!
    </div>
<?php endif; ?>


<!-- ========================= -->
<!-- USUÁRIOS -->
<!-- ========================= -->

<section>

    <h2>Usuários</h2>

    <form method="POST" action="/admin/test/user/create">

        <input
            type="text"
            name="username"
            placeholder="Usuário"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Senha"
            required
        >

        <select name="avatar">
            <option value="1">Avatar 1</option>
            <option value="2">Avatar 2</option>
        </select>

        <select name="role">
            <option value="user">Usuário</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">
            Criar usuário
        </button>

    </form>


    <table>

        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Avatar</th>
            <th>Role</th>
        </tr>

        <?php foreach ($users as $user): ?>

            <tr>

                <td><?= htmlspecialchars($user['id']) ?></td>

                <td><?= htmlspecialchars($user['username']) ?></td>

                <td><?= htmlspecialchars($user['avatar']) ?></td>

                <td><?= htmlspecialchars($user['role']) ?></td>

            </tr>

        <?php endforeach; ?>

    </table>

</section>


<!-- ========================= -->
<!-- NÍVEIS -->
<!-- ========================= -->

<section>

    <h2>Níveis</h2>

    <form method="POST" action="/admin/test/level/create">

        <button type="submit">
            Criar novo nível
        </button>

    </form>


    <table>

        <tr>
            <th>ID</th>
        </tr>

        <?php foreach ($levels as $level): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($level['id']) ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</section>


<!-- ========================= -->
<!-- PROGRESSO -->
<!-- ========================= -->

<section>

    <h2>Progresso</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Nível</th>
            <th>Concluído</th>
            <th>Acertos</th>
            <th>Erros</th>
        </tr>

        <?php foreach ($progress as $item): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($item['id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['user_id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['level_id']) ?>
                </td>

                <td>
                    <?= $item['completed'] ? 'Sim' : 'Não' ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['hits']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['errors']) ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</section>

</body>
</html>