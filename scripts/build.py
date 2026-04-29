# Un script qui va build mon code PHP parce qu'en toute honnêteté je sais pas du tout comment ça marche ce truc

import json
import os
import re
import shutil

def build():
	if os.path.exists(config['dist']):
		shutil.rmtree(config['dist'])

	if os.path.exists('config.json'):
		try:
			with open('config.json', 'r') as f:
				data = json.load(f)
				config.update(data)
		except FileNotFoundError:
			pass

	if os.path.exists(config['env']):
		with open(config['env'], 'r') as f:
			for line in f:
				if line.strip() and not line.startswith('#'):
					key, value = line.strip().split('=', 1)
					env[key] = value

	for root, dirs, files in os.walk(config['src']):
		for file in files:
			if file.endswith(".php"):
				with open(os.path.join(root, file), "r") as f:
					content = f.read()

				for key, value in env.items():
					field = re.search(rf"\${key} = " + r"[^;]+", content)

					if field:
						content = content.replace(field.group(), f"${key} = {value}")

				dist_path = os.path.join(config['dist'], os.path.relpath(root, config['src']))
				os.makedirs(dist_path, exist_ok = True)

				with open(os.path.join(dist_path, file), "w") as f:
					f.write(content)
			else:
				dist_path = os.path.join(config['dist'], os.path.relpath(root, config['src']))
				os.makedirs(dist_path, exist_ok = True)
				shutil.copy(os.path.join(root, file), os.path.join(dist_path, file))

if __name__ == "__main__":
	global config
	config = {
		'src': './src',
		'dist': './lib',
		'env': '.env'
	}

	global env
	env = {}

	build()
