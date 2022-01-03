# office tools

## What is this?

This is a library that operates on "office" files typically docx/xlsx/pptx and gives the ability to process them, password protect them, convert them to PDF / from html etc.

The goal isn't to have everything that could ever be done with an Office file in here, ideally if something can be done in pure PHP by editing the XML of a document in the main repo, that should be the the fisrt preference. Instead this repository handles cases that cannot be done purely in PHP. This is usually because it requires a full scale rendering engine so therefore needs some heavy peace of software such as libreoffice or similar. In addition, for some cases (docx to pdf conversion for example), customers are sensitive to the output "quality". This generally means that Microsoft office has to be in the flow somewhere, which isn't possible in the Linux environment we typically operate in.

So this library is an integration layer to another system that has the ability to process the files. Whilst this code could fairly easily live within the main docs repo, it is kept separate so that it can have a comprehensive test suite that actually runs tests against the third party system and validates the output quality. This isn't something we would want to be testing for every pull request to the docs repo.

## Implementation structure

There are several interfaces exposed in the root namespace that the main app can depend on. As of writing there are 2 implementations:

1. `legacy_windows` - interfaces with the old Windows servers we want to replace. These generally satisfy all our requirements (ppt to pptx conversion does not work however).
2. `convert_api_dot_com` - interfaces with convertapi.com - an online cloud service.

If we wanted to move away from convertapi.com - we could add a new implementation in this library, and as long as it satifies all the tests, we should be able to switch to it fairly easily without any major changes in docs.

We currently have:

- `WordConverter` - allows conversion of a word file (docx extension) to pdf.
- `ExcelConverter` - allows conversion of an excel file (xlsx extension) to pdf.
- `PowerpointConverter` - allows conversion of a powerpoint file (pptx extension) to pdf.
- `LegacyFormatConverter` - allows conversion from the old file formats (doc,xls,ppt) to the newer open xml formats (docx,xlsx,pptx). We don't operate on the legacy formats, but if somebody uploads one, we will convert it to the newer format on their behalf.
- `HtmlConverter` allows converting some html to either a word or excel file. The html the app uses (for word specifically) as some Microsoft specific tags for headers and footers to make those display on every page. So we are kind of forced to use a Microsoft based implementation here unless we build out a doc using xml directly.
- `WordFieldsUpdater` - will run an "update fields" procedure in a word file. This means that if the file has a table of contents, it will update the page numbers to reflect which pages content is rendered on. This is only needed if the input format is docx and output format docx. If using the `WordConverter` to convert the file to pdf, it is implied that the table of contents will be correct in the pdf. Some implementations of the `WordConverter` (mostly ms based) do this automatically, some don't.
- `WordProtecter` - will password protect a docx file encrypting it's contents. Whist technically it should be possible to do this in pure php as their is no rendering needing, there are no code libraries to do this currently.

## Contributing / Testing

The phpunit tests have some system dependencies in order to work. To run them locally you will need imagemagick installed (which gives the `convert` command), and openimageio which gives the `idiff` command. You may not want to install these locally so a Dockerfile is included which contains the dependencies.

If you have the dependencies installed you can run:

- `make test` - will run the tests normally
- `make test_cache` will use cached files from a previous run to avoid hitting API's where possible.

If you don't have/want to install the dependencies you can run:

- `make build` - to build the docker image
- `make test_in_docker` will run the tests normally
- `make test_in_docker_cache` will use the cached files from a previous run to avoid hitting API's where possible.

## How to use it.

You can get an instance of an implementation of either of the above interfaces using the factory. For example:

    $converter = WebmergeOfficeTools\Factory::wordConverter();

You can optionally pass an implementation name to the factory (`WebmergeOfficeTools::SYSTEM_LEGACY_WINDOWS` or `WebmergeOfficeTools::SYSTEM_CONVERTAPI_DOT_COM`)

Most of the converters accept a string input to reflect where the file is currently on disk, and a string output to reflect where the output should be persisted.

The library will throw a `WebmergeOfficeTools\Exceptions\ValidationException` if we fail because for example you pass the wrong file type etc. If an API errors then a `WebmergeOfficeTools\Exceptions\ApiException` will be thrown.


## Logging

You can create a logger that satisfies the `WebmergeOfficeTools\Logging\LoggingInterface` and call `WebmergeOfficeTools\Configuration::setLogger()` with an intance of it. Any implementation will be wrapped withsomething that times the output and will call either `info` or `error` on your logger with useful data.
