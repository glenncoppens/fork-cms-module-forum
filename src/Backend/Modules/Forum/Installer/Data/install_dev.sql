-- phpMyAdmin SQL Dump
-- version 4.3.2
-- http://www.phpmyadmin.net
--
-- Machine: 127.0.0.1
-- Gegenereerd op: 10 feb 2015 om 23:27
-- Serverversie: 5.6.12-log
-- PHP-versie: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `forkcms-modules`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `forum_categories`
--

CREATE TABLE IF NOT EXISTS `forum_categories` (
  `id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `meta_id`, `language`, `title`) VALUES
(1, 30, 'en', 'Default');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `forum_comments`
--

CREATE TABLE IF NOT EXISTS `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('comment','trackback') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'comment',
  `status` enum('published','moderation','spam') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'moderation',
  `data` text COLLATE utf8_unicode_ci COMMENT 'Serialized array with extra data'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int(11) NOT NULL COMMENT 'The real post id',
  `revision_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `status` enum('active','archived','draft') COLLATE utf8_unicode_ci NOT NULL,
  `publish_on` datetime NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `hidden` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `allow_comments` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `num_comments` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `revision_id`, `profile_id`, `title`, `text`, `status`, `publish_on`, `created_on`, `edited_on`, `hidden`, `allow_comments`, `num_comments`, `url`, `category_id`) VALUES
(1, 32, 1, 'Forum thread 1', '# Forum module\r\n\r\n### 1. Code blocks\r\n```\r\n<script>\r\n    function($log) {\r\n        console.log(''this is crazy'');\r\n        console.log($log);\r\n    }\r\n</script>\r\n```\r\n### 2) XSS proof (should be tested thouroughly)\r\n<script>alert(''hello'');</script>\r\nThe generated output in the preview is sanitized by the marked.js library.\r\nBy submitting this post the post will be filtered through a **server side markdown library**.\r\n\r\n### 3) Images and links\r\n![Google.com image](http://google.com/images/srpr/logo11w.png "Google.com title")\r\nThis is a [google.com](http://google.com/ "Google.com") inline link.\r\n\r\n[![Google.com image](http://google.com/images/srpr/logo11w.png "Google.com title")](http://google.com/ "Google.com")\r\n\r\n\r\n', 'active', '2015-02-07 22:33:02', '2015-02-07 22:33:02', '2015-02-07 22:33:02', 'N', 'Y', 0, 'forum-thread-1', 1),
(2, 33, 1, 'Title', '# this is an h1\r\n\r\nThis post is parsed through the github markdown api.\r\n----------------------------------------------------', 'active', '2015-02-08 19:03:59', '2015-02-08 19:03:59', '2015-02-08 19:03:59', 'N', 'Y', 0, 'title', 1),
(3, 34, 1, 'github preview', '```\r\n<script>\r\n    jsFrontend.forum.highlight.highlightAuto(code).value;\r\n    console.log(''This is a piece of text.'');\r\n</script>\r\n```\r\nTest\r\n\r\ntest br', 'active', '2015-02-08 19:27:35', '2015-02-08 19:27:35', '2015-02-08 19:27:35', 'N', 'Y', 0, 'github-preview', 1),
(4, 35, 1, 'Last added ', '# h1 or what\r\n```\r\n<script>\r\n    function($log) {\r\n        alert(''hahaha'');\r\n    }\r\n</script>\r\n```', 'active', '2015-02-08 20:08:59', '2015-02-08 20:08:59', '2015-02-08 20:08:59', 'N', 'Y', 0, 'last-added', 1),
(5, 36, 1, 'invalidate', '# invalidate frontend cache', 'active', '2015-02-08 20:34:47', '2015-02-08 20:34:47', '2015-02-08 20:34:47', 'N', 'Y', 0, 'invalidate', 1),
(6, 37, 1, 'tes tcache', '# h2 blbla\r\n\r\n<script>alert(''sdjf dskjf s'');</script>', 'active', '2015-02-08 22:07:47', '2015-02-08 22:07:47', '2015-02-08 22:07:47', 'N', 'Y', 0, 'tes-tcache', 1),
(7, 38, 1, 'img', '<img src="javascript:alert(''XSS'');">', 'active', '2015-02-08 22:17:14', '2015-02-08 22:17:14', '2015-02-08 22:17:14', 'N', 'Y', 0, 'img', 1),
(8, 39, 1, 'test', ''';alert(String.fromCharCode(88,83,83))//'';alert(String.fromCharCode(88,83,83))//";\r\nalert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//--\r\n></SCRIPT>">''><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>', 'active', '2015-02-08 22:20:05', '2015-02-08 22:20:05', '2015-02-08 22:20:05', 'N', 'Y', 0, 'test', 1),
(9, 40, 1, 'test 2', '<IMG SRC=javascript:alert(''XSS'')>', 'active', '2015-02-08 22:21:48', '2015-02-08 22:21:48', '2015-02-08 22:21:48', 'N', 'Y', 0, 'test-2', 1),
(10, 41, 1, 'test 3', '<IMG SRC=`javascript:alert("RSnake says, ''XSS''")`>', 'active', '2015-02-08 22:22:31', '2015-02-08 22:22:31', '2015-02-08 22:22:31', 'N', 'Y', 0, 'test-3', 1),
(11, 42, 1, 'test 4', '<a onmouseover="alert(document.cookie)">xxs link</a>', 'active', '2015-02-08 22:24:04', '2015-02-08 22:24:04', '2015-02-08 22:24:04', 'N', 'Y', 0, 'test-4', 1),
(12, 43, 1, 'test 5', '<IMG """><SCRIPT>alert("XSS")</SCRIPT>">', 'active', '2015-02-08 22:27:59', '2015-02-08 22:27:59', '2015-02-08 22:27:59', 'N', 'Y', 0, 'test-5', 1),
(13, 44, 1, 'test 6', '<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>', 'active', '2015-02-08 22:28:24', '2015-02-08 22:28:24', '2015-02-08 22:28:24', 'N', 'Y', 0, 'test-6', 1),
(14, 45, 1, 'test 7', '<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&\r\n#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>', 'active', '2015-02-08 22:31:17', '2015-02-08 22:31:17', '2015-02-08 22:31:17', 'N', 'Y', 0, 'test-7', 1),
(15, 46, 1, 'test 8', '<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>', 'active', '2015-02-08 22:33:35', '2015-02-08 22:33:35', '2015-02-08 22:33:35', 'N', 'Y', 0, 'test-8', 1),
(16, 47, 1, 'test 9', '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>', 'archived', '2015-02-08 22:34:42', '2015-02-08 22:34:42', '2015-02-08 22:34:42', 'N', 'Y', 0, 'test-9', 1),
(16, 60, 1, 'test 9', '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>\r\n\r\n# This is an h1\r\n\r\n```\r\n#text {\r\n    width: 99.5% !important;\r\n    min-height: 300px;\r\n}\r\n\r\n#preview {\r\n    width: 738px !important;\r\n    overflow-x: scroll;\r\n    min-height: 300px;\r\n}\r\n```', 'archived', '2015-02-08 22:34:00', '2015-02-08 22:34:42', '2015-02-10 21:14:26', 'N', 'Y', 0, 'test-9', 1),
(16, 61, 1, 'test 9', '.\r\n[normal link](javascript)\r\n.\r\n<p><a href="javascript">normal link</a></p>\r\n.\r\n\r\n\r\n\r\nShould not allow some protocols in links and images\r\n\r\n.\r\n[xss link](javascript:alert(1))\r\n\r\n[xss link](JAVASCRIPT:alert(1))\r\n\r\n[xss link](vbscript:alert(1))\r\n\r\n[xss link](VBSCRIPT:alert(1))\r\n\r\n[xss link](file:///123)\r\n.\r\n<p>[xss link](javascript:alert(1))</p>\r\n<p>[xss link](JAVASCRIPT:alert(1))</p>\r\n<p>[xss link](vbscript:alert(1))</p>\r\n<p>[xss link](VBSCRIPT:alert(1))</p>\r\n<p>[xss link](file:///123)</p>\r\n.\r\n\r\n\r\n.\r\n[xss link](&#34;&#62;&#60;script&#62;alert&#40;&#34;xss&#34;&#41;&#60;/script&#62;)\r\n\r\n[xss link](&#74;avascript:alert(1))\r\n\r\n[xss link](&#x26;#74;avascript:alert(1))\r\n\r\n.\r\n<p><a href="%22%3E%3Cscript%3Ealert(%22xss%22)%3C/script%3E">xss link</a></p>\r\n<p>[xss link](Javascript:alert(1))</p>\r\n<p>[xss link](&amp;#74;avascript:alert(1))</p>\r\n.\r\n\r\n.\r\n[xss link](<javascript:alert(1)>)\r\n.\r\n<p>[xss link](&lt;javascript:alert(1)&gt;)</p>\r\n.\r\n\r\n.\r\n[xss link](javascript&#x3A;alert(1))\r\n.\r\n<p>[xss link](javascript:alert(1))</p>\r\n.\r\n\r\n\r\nImage parser use the same code base.\r\n\r\n.\r\n![xss link](javascript:alert(1))\r\n.\r\n<p>![xss link](javascript:alert(1))</p>\r\n.\r\n\r\n\r\nAutolinks\r\n\r\n.\r\n<javascript&#x3A;alert(1)>\r\n\r\n<javascript:alert(1)>\r\n.\r\n<p>&lt;javascript:alert(1)&gt;</p>\r\n<p>&lt;javascript:alert(1)&gt;</p>\r\n.\r\n\r\n\r\nLinkifier\r\n\r\n.\r\njavascript&#x3A;alert(1)\r\n\r\njavascript:alert(1)\r\n.\r\n<p>javascript:alert(1)</p>\r\n<p>javascript:alert(1)</p>\r\n.\r\n\r\nReferences\r\n\r\n.\r\n[test]: javascript:alert(1)\r\n.\r\n<p>[test]: javascript:alert(1)</p>\r\n.', 'active', '2015-02-08 22:34:00', '2015-02-08 22:34:42', '2015-02-10 22:12:32', 'N', 'Y', 0, 'test-9', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_post_id_status` (`post_id`,`status`);

--
-- Indexen voor tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`revision_id`), ADD KEY `idx_status_language_hidden` (`status`,`hidden`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT voor een tabel `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT voor een tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `revision_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=62;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
