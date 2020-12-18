function confirmLink (event) {
	event.preventDefault();
	event.stopPropagation();

	confirm().then(() => {
		window.location = event.target.href;
	}).catch(() => {
		// don't do anything on reject
	})
}

function confirm (question = 'Are you sure', confirmString = 'Confirm', cancelString = 'Cancel') {
	return new Promise((resolve, reject) => {
		const modal = $('#confirmModal');
		const cancelBtn = $('#confirmModal .btn-secondary');
		const okBtn = $('#confirmModal .btn-primary');

		const confirm = () => {
			resolve();

			modal.modal('hide')
			okBtn.off('click', confirm);
			cancelBtn.off('click', cancel);
		}
		const cancel = () => {
			reject();

			modal.modal('hide')
			okBtn.off('click', confirm);
			cancelBtn.off('click', cancel);
		}

		okBtn.on('click', confirm);
		cancelBtn.on('click', cancel);

		modal.modal({
			show: true,
			backdrop: 'static',
		});
	})
}

function removeTranslationItemInput (event) {
	event.target.parentElement.parentElement.remove();
}


class TranslationImporter {
	constructor (template, containerSelector, fileInputId) {
		this.template = template;
		this.containerSelector = containerSelector;
		this.fileInputId = fileInputId;

		this.initListeners();
	}

	initListeners () {
		document.getElementById(this.fileInputId).addEventListener('change', (event) => this.handleFileChange(event));
	}

	handleFileChange (event) {
		const reader = new FileReader();
		const file = event.target.files[0];
		document.querySelector(`label[for="${this.fileInputId}"]`).innerText = file.name;
		reader.onload = (event) => this.parseFile(event, file);
		reader.readAsText(file);
	}

	parseFile (event, file) {
		const content = event.target.result;
		let parsedContent = {};
		switch (file.type) {
			case 'application/x-yaml':
				parsedContent = this.parseYaml(content);
				break;
			case 'text/csv':
				parsedContent = this.parseCsv(content);
				break;
			case 'text/xml':
				parsedContent = this.parseXml(content);
				break;
			case 'application/json':
				parsedContent = this.parseJson(content);
				break;
			default:
				throw new Error('Unsupported file type');

		}

		const container = $(this.containerSelector);
		container.html('<h5>Translations</h5>');

		let counter = 0;
		for (let key in parsedContent) {
			if (!parsedContent.hasOwnProperty(key)) {
				continue;
			}

			const input = this.template.replace(/%index%/g, counter++)
				.replace(/%key%/, key)
				.replace(/%value%/, parsedContent[key]);

			container.append($(input));
		}
	}

	parseXml (content) {
		try {
			const output = {};
			const parser = new DOMParser();
			const xmlDoc = parser.parseFromString(content, "text/xml");
			for (let translationNode of xmlDoc.getElementsByTagName('translation')) {

				const key = translationNode.querySelector('key').childNodes[0].nodeValue;
				const value = translationNode.querySelector('value').childNodes[0].nodeValue;

				output[key] = value;
			}
			return output;
		} catch (e) {
			alert('Invalid format provided');
			return {};
		}
	}

	parseYaml (content) {
		try {
			return jsyaml.load(content);
		} catch (e) {
			alert('Invalid format provided');
			return {};
		}
	}

	parseJson (content) {
		try {
			return JSON.parse(content);
		} catch (e) {
			alert('Invalid format provided');
			return {};
		}
	}

	parseCsv (content) {
		try {
			const parsedContent = Papa.parse(content);

			const output = {};

			// Skip first row since it is header definition
			for (let i = 1; i < parsedContent.data.length; i++) {
				const data = parsedContent.data[i];
				if (data.length !== 2) {
					continue;
				}

				const [key, value] = data;

				output[key] = value;
			}
			return output;
		} catch (e) {
			alert('Invalid format provided');
			return {};
		}
	}
}

(() => {
	for (let loadCallback of loadCallbacks) {
		loadCallback();
	}
})();
