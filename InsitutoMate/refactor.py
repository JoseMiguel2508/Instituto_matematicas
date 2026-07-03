import os
import re

models_dir = r"c:\Universidad\vivecoding\InsitutoMate\app\Models"

for filename in os.listdir(models_dir):
    if not filename.endswith('.php'):
        continue
    filepath = os.path.join(models_dir, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    relations_found = set()
    
    def replacer(match):
        func_name = match.group(1)
        rel_type = match.group(2)
        rel_class = rel_type[0].upper() + rel_type[1:]
        relations_found.add(rel_class)
        # Reconstruct the matched string with the return type
        return f"public function {func_name}(): {rel_class}\n    {{\n        return $this->{rel_type}"

    # Match functions without return types that return a relationship
    pattern = r"public function\s+([a-zA-Z0-9_]+)\s*\(\)\s*\{\s*return\s+\$this->(belongsTo|hasMany|hasOne|belongsToMany)"
    
    new_content = re.sub(pattern, replacer, content)
    
    if relations_found:
        imports_to_add = []
        for rel in relations_found:
            import_stmt = f"use Illuminate\\Database\\Eloquent\\Relations\\{rel};"
            if import_stmt not in new_content:
                imports_to_add.append(import_stmt)
        
        if imports_to_add:
            model_import = "use Illuminate\\Database\\Eloquent\\Model;"
            if model_import in new_content:
                imports_str = "\n".join(imports_to_add)
                new_content = new_content.replace(model_import, f"{model_import}\n{imports_str}")
            else:
                # If model import is not there, put it after namespace
                namespace_stmt = "namespace App\\Models;"
                if namespace_stmt in new_content:
                    imports_str = "\n".join(imports_to_add)
                    new_content = new_content.replace(namespace_stmt, f"{namespace_stmt}\n\n{imports_str}")
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filename}")
