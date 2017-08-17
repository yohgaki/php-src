--TEST--
hash_copy() via clone
--SKIPIF--
<?php extension_loaded('hash') or die('skip'); ?>
--FILE--
<?php

$algos = hash_algos();

foreach ($algos as $algo) {
	var_dump($algo);
	$orig = hash_init($algo);
	hash_update($orig, "I can't remember anything");
	$copy = clone $orig;
	var_dump(hash_final($orig));

	var_dump(hash_final($copy));
}

foreach ($algos as $algo) {
	var_dump($algo);
	$orig = hash_init($algo);
	hash_update($orig, "I can't remember anything");
	$copy = clone $orig;
	var_dump(hash_final($orig));

	hash_update($copy, "Can’t tell if this is true or dream");
	var_dump(hash_final($copy));
}

echo "Done\n";
?>
--EXPECTF--
string(3) "md2"
string(32) "d5ac4ffd08f6a57b9bd402b8068392ff"
string(32) "d5ac4ffd08f6a57b9bd402b8068392ff"
string(3) "md4"
string(32) "302c45586b53a984bd3a1237cb81c15f"
string(32) "302c45586b53a984bd3a1237cb81c15f"
string(3) "md5"
string(32) "e35759f6ea35db254e415b5332269435"
string(32) "e35759f6ea35db254e415b5332269435"
string(4) "sha1"
string(40) "29f62a228f726cd728efa7a0ac6a2aba318baf15"
string(40) "29f62a228f726cd728efa7a0ac6a2aba318baf15"
string(6) "sha224"
string(56) "51fd0aa76a00b4a86103895cad5c7c2651ec7da9f4fc1e50c43ede29"
string(56) "51fd0aa76a00b4a86103895cad5c7c2651ec7da9f4fc1e50c43ede29"
string(6) "sha256"
string(64) "d3a13cf52af8e9390caed78b77b6b1e06e102204e3555d111dfd149bc5d54dba"
string(64) "d3a13cf52af8e9390caed78b77b6b1e06e102204e3555d111dfd149bc5d54dba"
string(6) "sha384"
string(96) "6950d861ace4102b803ab8b3779d2f471968233010d2608974ab89804cef6f76162b4433d6e554e11e40a7cdcf510ea3"
string(96) "6950d861ace4102b803ab8b3779d2f471968233010d2608974ab89804cef6f76162b4433d6e554e11e40a7cdcf510ea3"
string(10) "sha512/224"
string(56) "a2573d0e3f6c3e2d174c935a35a8ea31032f04e9e83499ac3ceda568"
string(56) "a2573d0e3f6c3e2d174c935a35a8ea31032f04e9e83499ac3ceda568"
string(10) "sha512/256"
string(64) "fddacab80b3a610ba024c9d75a5fe0cafe5ae7c789f829b3c5fbea8ef11ccc1a"
string(64) "fddacab80b3a610ba024c9d75a5fe0cafe5ae7c789f829b3c5fbea8ef11ccc1a"
string(6) "sha512"
string(128) "caced3db8e9e3a5543d5b933bcbe9e7834e6667545c3f5d4087b58ec8d78b4c8a4a5500c9b88f65f7368810ba9905e51f1cff3b25a5dccf76634108fb4e7ce13"
string(128) "caced3db8e9e3a5543d5b933bcbe9e7834e6667545c3f5d4087b58ec8d78b4c8a4a5500c9b88f65f7368810ba9905e51f1cff3b25a5dccf76634108fb4e7ce13"
string(8) "sha3-224"
string(56) "7e1126cffee98e5c4b0e9dd5c6efabd5c9356d668e9a2d3cfab724d4"
string(56) "7e1126cffee98e5c4b0e9dd5c6efabd5c9356d668e9a2d3cfab724d4"
string(8) "sha3-256"
string(64) "834abfed9197af09cbe66b7748c65a050a3755ef7a556d6764eb6eabc93b4c7a"
string(64) "834abfed9197af09cbe66b7748c65a050a3755ef7a556d6764eb6eabc93b4c7a"
string(8) "sha3-384"
string(96) "c9016992586f7a8663c5379ed892349c1140ad258f7c44ee82f61f0b8cb75c675012ea94dc1314e06699be2d1465f67b"
string(96) "c9016992586f7a8663c5379ed892349c1140ad258f7c44ee82f61f0b8cb75c675012ea94dc1314e06699be2d1465f67b"
string(8) "sha3-512"
string(128) "5f85341bc9c6621406bf1841c4ce01727ea8759fdf2927106c3e70a75ad9fffd095b87f995aeee844e1a2c287e1195ce809b9bdb1c31258f7fc098175b6de0b4"
string(128) "5f85341bc9c6621406bf1841c4ce01727ea8759fdf2927106c3e70a75ad9fffd095b87f995aeee844e1a2c287e1195ce809b9bdb1c31258f7fc098175b6de0b4"
string(9) "ripemd128"
string(32) "5f1bc5f5aeaf747574dd34a6535cd94a"
string(32) "5f1bc5f5aeaf747574dd34a6535cd94a"
string(9) "ripemd160"
string(40) "02a2a535ee10404c6b5cf9acb178a04fbed67269"
string(40) "02a2a535ee10404c6b5cf9acb178a04fbed67269"
string(9) "ripemd256"
string(64) "547d2ed85ca0a0e3208b5ecf4fc6a7fc1e64db8ff13493e4beaf11e4d71648e2"
string(64) "547d2ed85ca0a0e3208b5ecf4fc6a7fc1e64db8ff13493e4beaf11e4d71648e2"
string(9) "ripemd320"
string(80) "785a7df56858f550966cddfd59ce14b13bf4b18e7892c4c1ad91bf23bf67639bd2c96749ba29cfa6"
string(80) "785a7df56858f550966cddfd59ce14b13bf4b18e7892c4c1ad91bf23bf67639bd2c96749ba29cfa6"
string(9) "whirlpool"
string(128) "6e60597340640e621e25f975cef2b000b0c4c09a7af7d240a52d193002b0a8426fa7da7acc5b37ed9608016d4f396db834a0ea2f2c35f900461c9ac7e5604082"
string(128) "6e60597340640e621e25f975cef2b000b0c4c09a7af7d240a52d193002b0a8426fa7da7acc5b37ed9608016d4f396db834a0ea2f2c35f900461c9ac7e5604082"
string(10) "tiger128,3"
string(32) "8d68e78bc5e62ba925a67aa48595cfc6"
string(32) "8d68e78bc5e62ba925a67aa48595cfc6"
string(10) "tiger160,3"
string(40) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e0"
string(40) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e0"
string(10) "tiger192,3"
string(48) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e08224fc35"
string(48) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e08224fc35"
string(10) "tiger128,4"
string(32) "a26ca3f58e74fb32ee44b099cb1b5122"
string(32) "a26ca3f58e74fb32ee44b099cb1b5122"
string(10) "tiger160,4"
string(40) "a26ca3f58e74fb32ee44b099cb1b512203375900"
string(40) "a26ca3f58e74fb32ee44b099cb1b512203375900"
string(10) "tiger192,4"
string(48) "a26ca3f58e74fb32ee44b099cb1b512203375900f30b741d"
string(48) "a26ca3f58e74fb32ee44b099cb1b512203375900f30b741d"
string(6) "snefru"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(9) "snefru256"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(4) "gost"
string(64) "5820c7c4a0650587538b30ef4099f2b5993069758d5c847a552e6ef7360766a5"
string(64) "5820c7c4a0650587538b30ef4099f2b5993069758d5c847a552e6ef7360766a5"
string(11) "gost-crypto"
string(64) "f7c4e35548d66aabe2b106f20515d289fde90969225d3d7b83f6dd12d694f043"
string(64) "f7c4e35548d66aabe2b106f20515d289fde90969225d3d7b83f6dd12d694f043"
string(7) "adler32"
string(8) "6f7c0928"
string(8) "6f7c0928"
string(5) "crc32"
string(8) "e5cfc160"
string(8) "e5cfc160"
string(6) "crc32b"
string(8) "69147a4e"
string(8) "69147a4e"
string(6) "fnv132"
string(8) "98139504"
string(8) "98139504"
string(7) "fnv1a32"
string(8) "aae4e042"
string(8) "aae4e042"
string(6) "fnv164"
string(16) "14522659f8138684"
string(16) "14522659f8138684"
string(7) "fnv1a64"
string(16) "bebc746a33b6ab62"
string(16) "bebc746a33b6ab62"
string(5) "joaat"
string(8) "aaebf370"
string(8) "aaebf370"
string(10) "haval128,3"
string(32) "86362472c8895e68e223ef8b3711d8d9"
string(32) "86362472c8895e68e223ef8b3711d8d9"
string(10) "haval160,3"
string(40) "fabdf6905f3ba18a3c93d6a16b91e31f7222a7a4"
string(40) "fabdf6905f3ba18a3c93d6a16b91e31f7222a7a4"
string(10) "haval192,3"
string(48) "e05d0ff5723028bd5494f32c0c2494cd0b9ccf7540af7b47"
string(48) "e05d0ff5723028bd5494f32c0c2494cd0b9ccf7540af7b47"
string(10) "haval224,3"
string(56) "56b196289d8de8a22296588cf90e5b09cb6fa1b01ce8e92bca40cae2"
string(56) "56b196289d8de8a22296588cf90e5b09cb6fa1b01ce8e92bca40cae2"
string(10) "haval256,3"
string(64) "ff4d7ab0fac2ca437b945461f9b62fd16e71e9103524d5d140445a00e3d49239"
string(64) "ff4d7ab0fac2ca437b945461f9b62fd16e71e9103524d5d140445a00e3d49239"
string(10) "haval128,4"
string(32) "ee44418e0195a0c4a35d112722919a9c"
string(32) "ee44418e0195a0c4a35d112722919a9c"
string(10) "haval160,4"
string(40) "f320cce982d5201a1ccacc1c5ff835a258a97eb1"
string(40) "f320cce982d5201a1ccacc1c5ff835a258a97eb1"
string(10) "haval192,4"
string(48) "a96600107463e8e97a7fe6f260d9bf4f4587a281caafa6db"
string(48) "a96600107463e8e97a7fe6f260d9bf4f4587a281caafa6db"
string(10) "haval224,4"
string(56) "7147c9e1c1e67b942da3229f59a1ab18f121f5d7f5765ca88bc9f200"
string(56) "7147c9e1c1e67b942da3229f59a1ab18f121f5d7f5765ca88bc9f200"
string(10) "haval256,4"
string(64) "82fec42679ed5a77a841962827b88a9cddf7d677736e50bc81f1a14b99f06061"
string(64) "82fec42679ed5a77a841962827b88a9cddf7d677736e50bc81f1a14b99f06061"
string(10) "haval128,5"
string(32) "8d0b157828328ae7d34d60b4b60c1dab"
string(32) "8d0b157828328ae7d34d60b4b60c1dab"
string(10) "haval160,5"
string(40) "54dab5e10dc41503f9b8aa32ffe3bab7cf1da8a3"
string(40) "54dab5e10dc41503f9b8aa32ffe3bab7cf1da8a3"
string(10) "haval192,5"
string(48) "7d91265a1b27698279d8d95a5ee0a20014528070bf6415e7"
string(48) "7d91265a1b27698279d8d95a5ee0a20014528070bf6415e7"
string(10) "haval224,5"
string(56) "7772b2e22f2a3bce917e08cf57ebece46bb33168619a776c6f2f7234"
string(56) "7772b2e22f2a3bce917e08cf57ebece46bb33168619a776c6f2f7234"
string(10) "haval256,5"
string(64) "438a602cb1a761f7bd0a633b7bd8b3ccd0577b524d05174ca1ae1f559b9a2c2a"
string(64) "438a602cb1a761f7bd0a633b7bd8b3ccd0577b524d05174ca1ae1f559b9a2c2a"
string(3) "md2"
string(32) "d5ac4ffd08f6a57b9bd402b8068392ff"
string(32) "5c36f61062d091a8324991132c5e8dbd"
string(3) "md4"
string(32) "302c45586b53a984bd3a1237cb81c15f"
string(32) "1d4196526aada3506efb4c7425651584"
string(3) "md5"
string(32) "e35759f6ea35db254e415b5332269435"
string(32) "f255c114bd6ce94aad092b5141c00d46"
string(4) "sha1"
string(40) "29f62a228f726cd728efa7a0ac6a2aba318baf15"
string(40) "a273396f056554dcd491b5dea1e7baa3b89b802b"
string(6) "sha224"
string(56) "51fd0aa76a00b4a86103895cad5c7c2651ec7da9f4fc1e50c43ede29"
string(56) "1aee028400c56ceb5539625dc2f395abf491409336ca0f3e177a50e2"
string(6) "sha256"
string(64) "d3a13cf52af8e9390caed78b77b6b1e06e102204e3555d111dfd149bc5d54dba"
string(64) "268e7f4cf88504a53fd77136c4c4748169f46ff7150b376569ada9c374836944"
string(6) "sha384"
string(96) "6950d861ace4102b803ab8b3779d2f471968233010d2608974ab89804cef6f76162b4433d6e554e11e40a7cdcf510ea3"
string(96) "0d44981d04bb11b1ef75d5c2932bd0aa2785e7bc454daac954d77e2ca10047879b58997533fc99650b20049c6cb9a6cc"
string(10) "sha512/224"
string(56) "a2573d0e3f6c3e2d174c935a35a8ea31032f04e9e83499ac3ceda568"
string(56) "cbc2bbf0028ed803af785b0f264962c84ec48d8ee0908322ef995ddb"
string(10) "sha512/256"
string(64) "fddacab80b3a610ba024c9d75a5fe0cafe5ae7c789f829b3c5fbea8ef11ccc1a"
string(64) "2cec704878ffa7128e0c4a61eef87d1f3c823184d364dfa3fed73beb00499b00"
string(6) "sha512"
string(128) "caced3db8e9e3a5543d5b933bcbe9e7834e6667545c3f5d4087b58ec8d78b4c8a4a5500c9b88f65f7368810ba9905e51f1cff3b25a5dccf76634108fb4e7ce13"
string(128) "28d7c721433782a880f840af0c3f3ea2cad4ef55de2114dda9d504cedeb110e1cf2519c49e4b5da3da4484bb6ba4fd1621ceadc6408f4410b2ebe9d83a4202c2"
string(8) "sha3-224"
string(56) "7e1126cffee98e5c4b0e9dd5c6efabd5c9356d668e9a2d3cfab724d4"
string(56) "9a21a5464794c2c9784df50cf89cf72234e11941bddaee93f912753e"
string(8) "sha3-256"
string(64) "834abfed9197af09cbe66b7748c65a050a3755ef7a556d6764eb6eabc93b4c7a"
string(64) "57aa7a90f29b5ab66592760592780da247fd39b4c911773687450f9df8cc8ed0"
string(8) "sha3-384"
string(96) "c9016992586f7a8663c5379ed892349c1140ad258f7c44ee82f61f0b8cb75c675012ea94dc1314e06699be2d1465f67b"
string(96) "5d6d7e42b241288bc707b74c50f90a37d69a4afa854ca72021a22cb379356e53b6233aea1be2f33d393d6effa9b5e36c"
string(8) "sha3-512"
string(128) "5f85341bc9c6621406bf1841c4ce01727ea8759fdf2927106c3e70a75ad9fffd095b87f995aeee844e1a2c287e1195ce809b9bdb1c31258f7fc098175b6de0b4"
string(128) "9b88c689bc13a36e6983b32e8ee9464d63b619f246ca451d1fe2a6c9670f01e71d0c8eb245f3204d27d27c056f2a0fef76a1e3bc30fb74cccbc984dbd4883ae6"
string(9) "ripemd128"
string(32) "5f1bc5f5aeaf747574dd34a6535cd94a"
string(32) "f95f5e22b8875ee0c48219ae97f0674b"
string(9) "ripemd160"
string(40) "02a2a535ee10404c6b5cf9acb178a04fbed67269"
string(40) "900d615c1abe714e340f4ecd6a3d65599fd30ff4"
string(9) "ripemd256"
string(64) "547d2ed85ca0a0e3208b5ecf4fc6a7fc1e64db8ff13493e4beaf11e4d71648e2"
string(64) "b9799db40d1af5614118c329169cdcd2c718db6af03bf945ea7f7ba72b8e14f4"
string(9) "ripemd320"
string(80) "785a7df56858f550966cddfd59ce14b13bf4b18e7892c4c1ad91bf23bf67639bd2c96749ba29cfa6"
string(80) "d6d12c1fca7a9c4a59c1be4f40188e92a746a035219e0a6ca1ee53b36a8282527187f7dffaa57ecc"
string(9) "whirlpool"
string(128) "6e60597340640e621e25f975cef2b000b0c4c09a7af7d240a52d193002b0a8426fa7da7acc5b37ed9608016d4f396db834a0ea2f2c35f900461c9ac7e5604082"
string(128) "e8c6a921e7d8eac2fd21d4df6054bb27a02321b2beb5b01b6f88c40706164e64d67ec97519bf76c8af8df896745478b78d42a0159f1a0db16777771fd9d420dc"
string(10) "tiger128,3"
string(32) "8d68e78bc5e62ba925a67aa48595cfc6"
string(32) "a99d2c0348d480dc0f3c35852926e0f1"
string(10) "tiger160,3"
string(40) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e0"
string(40) "a99d2c0348d480dc0f3c35852926e0f1e1825c16"
string(10) "tiger192,3"
string(48) "8d68e78bc5e62ba925a67aa48595cfc62cd1e5e08224fc35"
string(48) "a99d2c0348d480dc0f3c35852926e0f1e1825c1651957ee3"
string(10) "tiger128,4"
string(32) "a26ca3f58e74fb32ee44b099cb1b5122"
string(32) "66e2c0322421c4e5a9208e6aeed481e5"
string(10) "tiger160,4"
string(40) "a26ca3f58e74fb32ee44b099cb1b512203375900"
string(40) "66e2c0322421c4e5a9208e6aeed481e5c4b00448"
string(10) "tiger192,4"
string(48) "a26ca3f58e74fb32ee44b099cb1b512203375900f30b741d"
string(48) "66e2c0322421c4e5a9208e6aeed481e5c4b00448e344d9d0"
string(6) "snefru"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(64) "614ca924864fa0e8fa309aa0944e047d5edbfd4964a35858f4d8ec66a0fb88b0"
string(9) "snefru256"
string(64) "fbe88daa74c89b9e29468fa3cd3a657d31845e21bb58dd3f8d806f5179a85c26"
string(64) "614ca924864fa0e8fa309aa0944e047d5edbfd4964a35858f4d8ec66a0fb88b0"
string(4) "gost"
string(64) "5820c7c4a0650587538b30ef4099f2b5993069758d5c847a552e6ef7360766a5"
string(64) "a00961e371287c71c527a41c14564f13b6ed12ac7cd9d5f5dfb3542a25e28d3b"
string(11) "gost-crypto"
string(64) "f7c4e35548d66aabe2b106f20515d289fde90969225d3d7b83f6dd12d694f043"
string(64) "68ca9aea6729dc07d995fbe071a4b5c6490bb27fc4dc65ec0e96200d5e082996"
string(7) "adler32"
string(8) "6f7c0928"
string(8) "d9141747"
string(5) "crc32"
string(8) "e5cfc160"
string(8) "59f8d3d2"
string(6) "crc32b"
string(8) "69147a4e"
string(8) "3ee63999"
string(6) "fnv132"
string(8) "98139504"
string(8) "59ad036f"
string(7) "fnv1a32"
string(8) "aae4e042"
string(8) "fadc2cef"
string(6) "fnv164"
string(16) "14522659f8138684"
string(16) "5e8c64fba6a5ffcf"
string(7) "fnv1a64"
string(16) "bebc746a33b6ab62"
string(16) "893899e4415a920f"
string(5) "joaat"
string(8) "aaebf370"
string(8) "513479b4"
string(10) "haval128,3"
string(32) "86362472c8895e68e223ef8b3711d8d9"
string(32) "ebeeeb05c18af1e53d2d127b561d5e0d"
string(10) "haval160,3"
string(40) "fabdf6905f3ba18a3c93d6a16b91e31f7222a7a4"
string(40) "f1a2c9604fb40899ad502abe0dfcec65115c8a9a"
string(10) "haval192,3"
string(48) "e05d0ff5723028bd5494f32c0c2494cd0b9ccf7540af7b47"
string(48) "d3a7315773a326678208650ed02510ed96cd488d74cd5231"
string(10) "haval224,3"
string(56) "56b196289d8de8a22296588cf90e5b09cb6fa1b01ce8e92bca40cae2"
string(56) "6d7132fabc83c9ab7913748b79ecf10e25409569d3ed144177f46731"
string(10) "haval256,3"
string(64) "ff4d7ab0fac2ca437b945461f9b62fd16e71e9103524d5d140445a00e3d49239"
string(64) "7a469868ad4b92891a3a44524c58a2b8d0f3bebb92b4cf47d19bc6aba973eb95"
string(10) "haval128,4"
string(32) "ee44418e0195a0c4a35d112722919a9c"
string(32) "6ecddb39615f43fd211839287ff38461"
string(10) "haval160,4"
string(40) "f320cce982d5201a1ccacc1c5ff835a258a97eb1"
string(40) "bcd2e7821723ac22e122b8b7cbbd2daaa9a862df"
string(10) "haval192,4"
string(48) "a96600107463e8e97a7fe6f260d9bf4f4587a281caafa6db"
string(48) "ae74619a88dcec1fbecde28e27f009a65ecc12170824d2cd"
string(10) "haval224,4"
string(56) "7147c9e1c1e67b942da3229f59a1ab18f121f5d7f5765ca88bc9f200"
string(56) "fdaba6563f1334d40de24e311f14b324577f97c3b78b9439c408cdca"
string(10) "haval256,4"
string(64) "82fec42679ed5a77a841962827b88a9cddf7d677736e50bc81f1a14b99f06061"
string(64) "289a2ba4820218bdb25a6534fbdf693f9de101362584fdd41e32244c719caa37"
string(10) "haval128,5"
string(32) "8d0b157828328ae7d34d60b4b60c1dab"
string(32) "ffa7993a4e183b245263fb1f63e27343"
string(10) "haval160,5"
string(40) "54dab5e10dc41503f9b8aa32ffe3bab7cf1da8a3"
string(40) "375ee5ab3a9bd07a1dbe5d071e07b2afb3165e3b"
string(10) "haval192,5"
string(48) "7d91265a1b27698279d8d95a5ee0a20014528070bf6415e7"
string(48) "c650585f93c6e041e835caedc621f8c42d8bc6829fb76789"
string(10) "haval224,5"
string(56) "7772b2e22f2a3bce917e08cf57ebece46bb33168619a776c6f2f7234"
string(56) "bc674d465a822817d939f19b38edde083fe5668759836c203c56e3e4"
string(10) "haval256,5"
string(64) "438a602cb1a761f7bd0a633b7bd8b3ccd0577b524d05174ca1ae1f559b9a2c2a"
string(64) "da70ad9bd09ed7c9675329ea2b5279d57761807c7aeac6340d94b5d494809457"
Done
