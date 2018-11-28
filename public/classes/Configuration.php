<?php

/**
 * Utility class to access the applications configuration.
 *
 */
final class Configuration
{

    /**
     * Retrieves a node from the configuration file.
     *
     * @param string $node
     *            Name of the node.
     */
    public static function getNode(string $node)
    {
        $config = yaml_parse_file("config.yml");
        return $config[$node];
    }
}

