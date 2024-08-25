```markdown
# Gerenciador de Times de Futebol

Este aplicativo gerencia times de futebol com base nas habilidades dos jogadores e na presença confirmada. Ele permite adicionar jogadores, atualizar seus detalhes e atribuí-los aleatoriamente a times. O sistema foi desenvolvido utilizando a arquitetura de microserviços e é executado em um ambiente Docker, garantindo fácil implantação e manutenção.

## Tecnologias Utilizadas

- PHP (Laravel)
- MySQL
- Docker
- Nginx
- React (Frontend)
- AWS (para hospedagem)

## Configuração

1. Clone o repositório:

   ```bash
   git clone <url_do_repositorio>
   cd soccer_team_manager
   ```

2. Inicie os containers Docker:

   ```bash
   docker-compose up -d --build
   ```

3. Execute as migrações para criar as tabelas no banco de dados:

   ```bash
   docker-compose exec app php artisan migrate
   ```

4. Execute os Seeders para popular o banco de dados com dados iniciais:

   ```bash
   docker-compose exec app php artisan db:seed
   ```

   > **Nota**: Os seeders criam jogadores fictícios para que você possa testar o sistema sem a necessidade de criar dados manualmente.

5. (Opcional) Para rodar as migrações e seeders em um único comando:

   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

## Endpoints da API

### Listar Todos os Jogadores

```bash
curl http://localhost:8080/api/players
```

### Criar um Jogador

```bash
curl -X POST http://localhost:8080/api/players -H "Content-Type: application/json" -d '{"name":"John Doe","level":4,"is_goalkeeper":false}'
```

### Atualizar um Jogador

```bash
curl -X PUT http://localhost:8080/api/players/1 -H "Content-Type: application/json" -d '{"name":"John Doe","level":5,"is_goalkeeper":true}'
```

### Excluir Logicamente um Jogador

```bash
curl -X DELETE http://localhost:8080/api/players/1
```

### Sortear Times

```bash
curl -X POST http://localhost:8080/api/players/sort -H "Content-Type: application/json" -d '{"players_per_team":6}'
```

## Estrutura do Projeto

Este projeto segue a estrutura MVC do Laravel, com as seguintes principais funcionalidades:

- **Player Management**: Adicionar, atualizar, excluir jogadores e listar todos os jogadores registrados.
- **Team Sorting**: Um algoritmo para distribuir jogadores em times, garantindo um balanceamento com base no nível dos jogadores.
- **Frontend React**: A interface de usuário desenvolvida com React que consome os endpoints da API Laravel.
- **Docker**: Toda a aplicação é empacotada e executada em containers Docker, garantindo portabilidade e consistência entre os ambientes de desenvolvimento e produção.

## Rodando os Seeders

Os Seeders são utilizados para popular a base de dados com dados iniciais. Isso é útil para testes ou para começar com uma base de dados já populada.

Para rodar os seeders, use o seguinte comando:

```bash
docker-compose exec app php artisan db:seed
```

## Notas

- Certifique-se de que o Docker esteja instalado e em execução.
- O arquivo `.env` deve estar configurado corretamente para o seu ambiente. Um exemplo do conteúdo do arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=football_db
DB_PORT=3306
DB_DATABASE=soccer_team_manager
DB_USERNAME=root
DB_PASSWORD=secret
```
```
