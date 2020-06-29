
--
-- Table structure for table `hit_detail`
--

CREATE TABLE `hit_detail` (
                              `id` int(11) UNSIGNED NOT NULL,
                              `url_id` int(11) UNSIGNED NOT NULL,
                              `accessed` datetime NOT NULL,
                              `browser_name` varchar(63) NOT NULL,
                              `browser_version` varchar(63) NOT NULL,
                              `os` varchar(63) NOT NULL,
                              `ip` varchar(63) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `urls`
--

CREATE TABLE `urls` (
                        `id` int(10) UNSIGNED NOT NULL,
                        `url` varchar(1000) NOT NULL,
                        `created` datetime NOT NULL,
                        `accessed` datetime DEFAULT NULL,
                        `hits` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for table `hit_detail`
--
ALTER TABLE `hit_detail`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `urls`
--
ALTER TABLE `urls`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `url` (`url`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hit_detail`
--
ALTER TABLE `hit_detail`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `urls`
--
ALTER TABLE `urls`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;