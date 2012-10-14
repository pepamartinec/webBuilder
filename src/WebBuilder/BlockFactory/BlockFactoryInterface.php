<?php
namespace WebBuilder\BlockFactory;

interface BlockFactoryInterface
{
    /**
     * Returns the name of the namespaces handled by this block factory.
     *
     * @return string[]
     */
    public function getNamespaceNames();

	/**
	 * Creates a new block object
	 *
	 * @param  string $blockObjectName
	 * @return WebBlockInterface
	 *
	 * @throws FactoryNotFoundException when no suitable factory was found
	 */
	public function createBlock($blockObjectName);
}