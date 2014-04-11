<?php

echo "<strong>" . get_class($this->exception) . ": " . $this->exception->getMessage() . "</strong> at <strong>" . $this->exception->getFile() . "</strong> on line <strong>" . $this->exception->getLine() . "</strong>";
echo "<h2>Stack trace:</h2>";
echo "<pre>";
print_r($this->exception->getTraceAsString());
echo "</pre>";