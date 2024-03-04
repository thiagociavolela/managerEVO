CREATE TABLE `config_api` (
  `id` int NOT NULL,
  `url_api` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `config_api` (`id`, `url_api`, `api_key`) VALUES
(1, 'http://ip:porta', 'sua_Api_Key');


ALTER TABLE `config_api`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `config_api`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
